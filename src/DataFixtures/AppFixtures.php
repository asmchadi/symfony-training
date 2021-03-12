<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    const NB_PRODUCTS = 15;
    const NB_CATEGORIES = 6;

    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadCategories($manager);
        $this->loadProducts($manager);
        $manager->flush();
    }

    private function loadCategories(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_CATEGORIES; $i++) {
            $category = new Category();
            $category->setLabel($this->faker->sentence(1));
            $manager->persist($category);
            $this->addReference('CAT'.$i, $category);
        }
    }

    private function loadProducts(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_PRODUCTS; $i++) {
            /** @var Category $category */
            $category = $this->getReference('CAT'.\rand(1, self::NB_CATEGORIES));
            $product = new Product();
            $product->setLabel($this->faker->sentence(5))
                ->setCover('https://picsum.photos/255/309')
                ->setDescription($this->faker->paragraph(3))
                ->setUnitPrice($this->faker->randomFloat(2, 10, 150))
                ->addCategory($category)
                ->setQuantity(\mt_rand(0, 18));
            $manager->persist($product);
        }
    }
}
