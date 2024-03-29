<?php namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\ServiceModule;
use Subscribo\ModelCore\Models\ServicePool;
use Subscribo\ModelCore\Models\Locale;
use Subscribo\ModelCore\Models\Currency;
use Subscribo\ModelCore\Models\Country;
use Subscribo\ModelCore\Models\OAuthConfiguration;

class ServiceSeeder extends Seeder {

    public function run()
    {
        $euro = Currency::firstOrCreate(['identifier' => 'EUR']);
        $dollar = Currency::firstOrCreate(['identifier' => 'USD']);
        $pound = Currency::firstOrCreate(['identifier' => 'GBP']);

        $austria = Country::firstOrCreate(['identifier' => 'AT']);
        $germany = Country::firstOrCreate(['identifier' => 'DE']);
        $slovakia = Country::firstOrCreate(['identifier' => 'SK']);
        $czechRepublic = Country::firstOrCreate(['identifier' => 'CZ']);
        $unitedKingdom = Country::firstOrCreate(['identifier' => 'GB']);
        $unitedStates = Country::firstOrCreate(['identifier' => 'US']);

        $frontendEnglish = Locale::firstOrCreate(['identifier' => 'en_US-FRONTEND']);
        $frontendGerman = Locale::firstOrCreate(['identifier' => 'de_AT-FRONTEND']);
        $frontendSlovak = Locale::firstOrCreate(['identifier' => 'sk_SK-FRONTEND']);
        $frontendService = Service::firstOrNew(['identifier' => 'FRONTEND']);
        $frontendService->url = 'http://frontend.sio.kochabo.at';
        $frontendService->name = 'Frontend';
        $frontendService->deliveryPeriod = '1 week';
        $frontendService->deliveryAutoAddLimit = '2 months';
        $frontendService->deliveryAutoAvailableStart = 'today';
        $frontendService->deliveryAutoAvailableEnd = '1 month';
        $frontendService->defaultLocale()->associate($frontendGerman);
        $frontendService->addCountries([$austria, $germany, $slovakia, $czechRepublic], [$euro, $dollar]);
        $frontendService->addCountries($unitedKingdom, [$pound, $euro]);
        $frontendService->addCountries($unitedStates, $dollar);
        $frontendService->save();
        $frontendService->translateOrNew('de')->name = 'Frontend zum testen';
        $frontendService->translateOrNew('sk')->name = 'Testovací Frontend';
        $frontendService->save();
        $frontendService->availableLocales()->attach($frontendEnglish);
        $frontendService->availableLocales()->attach($frontendGerman);
        $frontendService->availableLocales()->attach($frontendSlovak);
        ServiceModule::enableModule($frontendService, ServiceModule::MODULE_ACCOUNT_MERGING);
        ServiceModule::enableModule($frontendService, ServiceModule::MODULE_WIDGET);

        if (env('FACEBOOK_APP_CLIENT_ID')) {
            $oAuthConfiguration = new OAuthConfiguration();
            $oAuthConfiguration->serviceId = $frontendService->id;
            $oAuthConfiguration->provider = 'facebook';
            $oAuthConfiguration->identifier = env('FACEBOOK_APP_CLIENT_ID');
            $oAuthConfiguration->secret = env('FACEBOOK_APP_CLIENT_SECRET');
            $oAuthConfiguration->scopes = json_encode('email');
            $oAuthConfiguration->redirect = null;
            $oAuthConfiguration->save();
        }
        if (env('TWITTER_APP_CLIENT_ID')) {
            $oAuthConfiguration2 = new OAuthConfiguration();
            $oAuthConfiguration2->serviceId = $frontendService->id;
            $oAuthConfiguration2->provider = 'twitter';
            $oAuthConfiguration2->identifier = env('TWITTER_APP_CLIENT_ID');
            $oAuthConfiguration2->secret = env('TWITTER_APP_CLIENT_SECRET');
            $oAuthConfiguration2->scopes = null;
            $oAuthConfiguration2->redirect = null;
            $oAuthConfiguration2->save();
        }

        $american = Locale::firstOrNew(['identifier' => 'en_US']);
        $british = Locale::firstOrNew(['identifier' => 'en_GB']);
        $german = Locale::firstOrNew(['identifier' => 'de']);
        $test2Service = Service::firstOrNew(['identifier' => 'MAIN']);
        $test2Service->url = 'http://subscribo.localhost';
        $test2Service->name = 'Main';
        $test2Service->deliveryAutoAddLimit = '2 months';
        $test2Service->deliveryAutoAvailableEnd = '1 month';
        $test2Service->defaultLocale()->associate($american);
        $test2Service->translateOrNew('de')->name = 'Haupt';
        $test2Service->addCountries([$austria, $germany, $slovakia, $czechRepublic], $euro);
        $test2Service->addCountries($unitedKingdom, [$pound, $euro]);
        $test2Service->save();
        $test2Service->availableLocales()->attach($american);
        $test2Service->availableLocales()->attach($british);
        $test2Service->availableLocales()->attach($german);

        ServiceModule::enableModule($test2Service, ServiceModule::MODULE_ACCOUNT_MERGING);
        ServiceModule::enableModule($test2Service, ServiceModule::MODULE_WIDGET);

        $test3Service = Service::firstOrNew(['identifier' => 'TEST3']);
        $test3Service->name = 'Test3 in Pool3';
        $test3Service->deliveryPeriod = '1 month';
        $test3Service->deliveryAutoAddLimit = '5 months';
        $test3Service->deliveryAutoAvailableStart = '2 weeks';
        $test3Service->deliveryAutoAvailableEnd = '3 month';

        $test3Service->defaultLocale()->associate($american);
        $test3Service->addCountries([$austria, $germany, $slovakia], $euro);
        $test3Service->save();
        $test3Service->availableLocales()->attach($american);
        $test3Service->availableLocales()->attach($british);

        ServiceModule::enableModule($test3Service, ServiceModule::MODULE_ACCOUNT_MERGING);
        ServiceModule::enableModule($test3Service, ServiceModule::MODULE_WIDGET);

        $anotherService = Service::firstOrNew(['identifier' => 'ANOTHER']);
        $anotherService->name = 'Another Service';
        $anotherService->defaultLocale()->associate($american);
        $anotherService->addCountries([$austria, $germany, $slovakia], $euro);
        $anotherService->save();
        $anotherService->availableLocales()->attach($american);
        $anotherService->availableLocales()->attach($british);

        ServiceModule::enableModule($anotherService, ServiceModule::MODULE_ACCOUNT_MERGING);
        ServiceModule::enableModule($anotherService, ServiceModule::MODULE_WIDGET);

        $servicePool2 = ServicePool::firstOrCreate(['identifier' => 'POOL2']);
        $servicePool2->services()->attach($frontendService);
        $servicePool2->services()->attach($test2Service);
        $servicePool2->save();

        $servicePool3 = ServicePool::firstOrCreate(['identifier' => 'POOL3']);
        $servicePool3->services()->attach($frontendService);
        $servicePool3->services()->attach($test3Service);
        $servicePool3->save();
    }
}
