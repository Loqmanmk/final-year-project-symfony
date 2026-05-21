<?php

namespace App\Service;

use App\Dto\CartItemInput;
use App\Entity\CustomerOrder;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CartHandler
{
    public function __construct(
        #[Autowire(service: SessionCart::class)]
        private readonly CartInterface $cart,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function add(Product $product, int $quantity): void
    {
        if (null === $product->getId()) {
            throw new \InvalidArgumentException('Le produit doit etre persiste avant ajout au panier.');
        }

        $this->cart->add(new CartItemInput($product->getId(), $quantity));
    }

    public function update(Product $product, int $quantity): void
    {
        if (null !== $product->getId()) {
            $this->cart->updateQuantity($product->getId(), $quantity);
        }
    }

    public function remove(Product $product): void
    {
        if (null !== $product->getId()) {
            $this->cart->remove($product->getId());
        }
    }

    /**
     * @return array<int, array{product: Product, quantity: int, lineTotal: float}>
     */
    public function lines(): array
    {
        $lines = [];

        foreach ($this->cart->all() as $productId => $quantity) {
            $product = $this->productRepository->find($productId);

            if (!$product instanceof Product) {
                continue;
            }

            $lines[] = [
                'product' => $product,
                'quantity' => $quantity,
                'lineTotal' => $product->getPriceAsFloat() * $quantity,
            ];
        }

        return $lines;
    }

    public function total(): float
    {
        return array_sum(array_column($this->lines(), 'lineTotal'));
    }

    public function count(): int
    {
        return $this->cart->count();
    }

    public function clear(): void
    {
        $this->cart->clear();
    }

    public function saveFor(User $user): CustomerOrder
    {
        $order = (new CustomerOrder())
            ->setCustomer($user)
            ->setStatus('Panier enregistre')
            ->setTotal($this->money($this->total()));

        foreach ($this->lines() as $line) {
            $product = $line['product'];
            $lineTotal = $line['lineTotal'];

            $order->addItem(
                (new OrderItem())
                    ->setProductName((string) $product->getName())
                    ->setQuantity($line['quantity'])
                    ->setUnitPrice($this->money($product->getPriceAsFloat()))
                    ->setLineTotal($this->money($lineTotal))
            );
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();
        $this->clear();

        return $order;
    }

    private function money(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
