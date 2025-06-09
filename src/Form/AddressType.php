<?php

namespace App\Form;

use App\Entity\VolunteerProfile;
use App\Geocoding\GeocodingService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function __construct(
        private readonly GeocodingService $geocodingService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'label' => 'Street Address',
                'attr' => [
                    'placeholder' => 'Enter your street address',
                    'class' => 'form-control',
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Postal Code',
                'attr' => [
                    'placeholder' => 'Enter your postal code',
                    'class' => 'form-control',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'attr' => [
                    'placeholder' => 'Enter your city',
                    'class' => 'form-control',
                ],
            ])
            ->add('country', CountryType::class, [
                'label' => 'Country',
                'attr' => [
                    'class' => 'form-select',
                ],
                'placeholder' => 'Select your country',
            ]);

        // Add form event listener to handle geocoding
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $profile = $event->getData();
            if (!$profile instanceof VolunteerProfile) {
                return;
            }

            // Only geocode if we have at least an address and city
            if ($profile->getAddress() && $profile->getCity()) {
                $geocoded = $this->geocodingService->geocodeAddress(
                    $profile->getAddress(),
                    $profile->getPostalCode(),
                    $profile->getCity(),
                    $profile->getCountry()
                );

                if ($geocoded['latitude'] && $geocoded['longitude']) {
                    $profile->setLatitude($geocoded['latitude']);
                    $profile->setLongitude($geocoded['longitude']);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VolunteerProfile::class,
        ]);
    }
}
