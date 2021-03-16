<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    const NB_PRODUCTS = 15;
    const NB_CATEGORIES = 6;

    private $faker;
    /**
     * @var PasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->faker = Factory::create();
        $this->encoder = $encoder;
    }

    /**
     * Performs the loading of fake data.
     *
     * @param ObjectManager $manager the object manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadAdmins($manager);
        $this->loadCategories($manager);
        $this->loadProducts($manager);
        $manager->flush();
    }

    /**
     * Loads some dummy categories.
     *
     * @param ObjectManager $manager the object manager
     */
    private function loadCategories(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_CATEGORIES; $i++) {
            $category = new Category();
            $category->setLabel($this->faker->sentence(1));
            $manager->persist($category);
            $this->addReference('CAT' . $i, $category);
        }
    }

    /**
     * Loads some dummy products.
     *
     * @param ObjectManager $manager the object manager
     */
    private function loadProducts(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_PRODUCTS; $i++) {
            /** @var Category $category */
            $category = $this->getReference('CAT' . \rand(1, self::NB_CATEGORIES));
            $product = new Product();
            $product->setLabel($this->faker->sentence(5))
                ->setCover('https://picsum.photos/255/309')
                ->setShortDescription($this->faker->paragraph(10))
                ->setDescription('<p>' . \implode('</p><p>', $this->faker->paragraphs(10)) . '</p>')
                ->setUnitPrice($this->faker->randomFloat(2, 10, 150))
                ->addCategory($category)
                ->setQuantity(\mt_rand(0, 18));
            $manager->persist($product);
        }
    }

    private function loadAdmins(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setEmail('ikhadiri@sqli.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->encoder->encodePassword($admin, '123456'));
        $manager->persist($admin);
    }
}
