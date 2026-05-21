<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:seed-demo', description: 'Cree les categories, produits et utilisateur de demonstration.')]
class SeedDemoDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->entityManager->getRepository(Product::class)->count([]) > 0) {
            $output->writeln('Les donnees de demonstration existent deja.');

            return Command::SUCCESS;
        }

        $electronics = $this->category('Electronics', 'electronics', 'Casques, souris et accessoires connectes.', 'airbod.png');
        $fashion = $this->category('Fashion', 'fashion', 'Articles utiles pour un style simple et soigne.', 'thumbnail.png');
        $home = $this->category('Home and Garden', 'home-garden', 'Objets pratiques pour la maison.', 'item.png');
        $books = $this->category('Books', 'books', 'Guides et ressources pour apprendre.', 'profile.png');

        $this->product($electronics, 'Wireless Headphones', 'wireless-headphones', 'Casque sans fil confortable avec reduction de bruit et autonomie longue duree.', '79.99', 'airbod.png', true);
        $this->product($electronics, 'Gaming Mouse', 'gaming-mouse', 'Souris precise avec boutons programmables et design ergonomique.', '49.99', 'mouse.png', true);
        $this->product($fashion, 'Classic Leather Jacket', 'classic-leather-jacket', 'Veste elegante, durable, parfaite pour les sorties de mi-saison.', '149.99', 'thumbnail.png', true);
        $this->product($home, 'Smart Plant Sensor', 'smart-plant-sensor', 'Capteur pour suivre humidite, lumiere et sante de vos plantes.', '34.99', 'item.png', true);
        $this->product($home, 'Premium Yoga Mat', 'premium-yoga-mat', 'Tapis antiderapant epais pour yoga, sport et etirements.', '29.99', 'item.png', false);
        $this->product($books, 'Web Development Guide', 'web-development-guide', 'Guide clair pour reviser HTML, CSS, PHP et les bases Symfony.', '24.99', 'profile.png', false);

        if (!$this->entityManager->getRepository(User::class)->findOneBy(['email' => 'demo@example.com'])) {
            $user = (new User())
                ->setEmail('demo@example.com')
                ->setFullName('Client Demo')
                ->setAddress('123 Rue Example, Casablanca')
                ->setPhone('+212 600 000 000');
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();
        $output->writeln('Donnees de demonstration creees.');

        return Command::SUCCESS;
    }

    private function category(string $name, string $slug, string $description, string $image): Category
    {
        $category = (new Category())
            ->setName($name)
            ->setSlug($slug)
            ->setDescription($description)
            ->setImageFilename($image);

        $this->entityManager->persist($category);

        return $category;
    }

    private function product(Category $category, string $name, string $slug, string $description, string $price, string $image, bool $isTop): Product
    {
        $product = (new Product())
            ->setCategory($category)
            ->setName($name)
            ->setSlug($slug)
            ->setDescription($description)
            ->setPrice($price)
            ->setImageFilename($image)
            ->setIsTop($isTop);

        $this->entityManager->persist($product);

        return $product;
    }
}
