<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Phone;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $_passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->_passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $suppliers = [
            'Apple', 'Samsung', 'Xiaomi', 'Huawei', 'Crosscall', 'Nokia', 'LG'
        ];

        $featuresSet = [
            'Battery', 'Screen Size', 'Screen Resolution', 'Camera', 'Memory RAM'
        ];

        $faker = Factory::create('fr_FR');

        //// PHONES ////

        for ($i = 0; $i < 200; $i++) {
            $phone = new Phone();

            $phone->setName($faker->word)
                ->setSupplier($suppliers[array_rand($suppliers)])
                ->setColor($faker->colorName)
                ->setProductReference($faker->randomLetter.$faker->randomNumber(7));

            foreach ($featuresSet as $feature) {
                $features[$feature] = $this->setFeatureValue($feature);
            }

            $phone->setFeatures($features);

            $manager->persist($phone);
        }

        //// COMPANIES ////

        for ($i = 0; $i < 15; $i++) {
            $company = new Company();
            $companyName = $faker->company;

            $company
                ->setName($faker->company)
                ->setPassword($this->_passwordEncoder->encodePassword($company, 'azerty'))
                ->setEmail('contact@'.strtolower(str_replace(' ', '-', $companyName)).'.com')
                ->setStreetNumber($faker->buildingNumber)
                ->setStreetName($faker->streetName)
                ->setCity($faker->city)
                ->setCountry($faker->country)
                ->setPostCode($faker->postcode)
                ->setVatNumber($faker->vat)
                ->setPhoneNumber($faker->phoneNumber);

            $numberOfUsers = rand(0, 50);

            for ($j = 0; $j < $numberOfUsers; $j++) {
                $user = new User();

                $user->setEmail($faker->email)
                    ->setFirstName($faker->firstName)
                    ->setLastName($faker->lastName)
                    ->setPhoneNumber($faker->phoneNumber)
                    ->setCompany($company);

                $manager->persist($user);
            }

            $manager->persist($company);
        }

        $manager->flush();
    }

    private function setFeatureValue($feature)
    {
        $faker = Factory::create();

        switch ($feature) {
            case 'Battery': return $faker->numberBetween(1100, 6000).'mA';
            break;

            case 'Screen Size':
                $sizes = ['4"', '4.7"', '5.5"', '7"', '6.1"'];

                return $sizes[array_rand($sizes)];
            break;

            case 'Screen Resolution': return $faker->numberBetween(720, 1780).' pixels';
            break;

            case 'Camera': return $faker->numberBetween(8, 12).' megapixels';
            break;

            case 'Memory RAM':
                $memories = ['2GB', '4GB', '8GB', '16GB'];

                return $memories[array_rand($memories)];
            break;

            default:
            return 'NA';
            break;
        }
    }
}
