Getting and setting translations
First, you must prepare your model as instructed in the installation instructions.

##Setting a translation
The easiest way to set a translation for the current locale is to just set the property for a translatable attribute.

Here's an example, given that name is a translatable attribute:

$newsItem->name = 'New translation';
To actually save the translation, don't forget to save your model.

$newsItem->name = 'New translation'
$newsItem->save();
You can immediately set translations when creating a model.

NewsItem::create([
   'name' => [
      'en' => 'Name in English',
      'nl' => 'Naam in het Nederlands'
   ],
]);
To set a translation for a specific locale you can use this method:

public function setTranslation(string $attributeName, string $locale, string $value)
You can set translations for multiple languages with

$translations = ['en' => 'hello', 'es' => 'hola'];
$newItem->name = $translations;

// alternatively, use the `setTranslations` method

$newsItem->setTranslations('name', $translations);

$newItem->save();
##Getting a translation
The easiest way to get a translation for the current locale is to just get the property for the translated attribute. For example (given that name is a translatable attribute):

$newsItem->name;
You can also use this method:

public function getTranslation(string $attributeName, string $locale, bool $useFallbackLocale = true) : string
This function has an alias named translate.

##Getting all translations
You can get all translations by calling getTranslations() without an argument:

$newsItem->getTranslations();
Or you can use the accessor:

$yourModel->translations
The methods above will give you back an array that holds all translations, for example:

$newsItem->getTranslations('name'); 
// returns ['en' => 'Name in English', 'nl' => 'Naam in het Nederlands']
The method above returns, all locales. If you only want specific locales, pass that to the second argument of getTranslations.

public function getTranslations(string $attributeName, array $allowedLocales): array
Here's an example:

$translations = [
    'en' => 'Hello',
    'fr' => 'Bonjour',
    'de' => 'Hallo',
];

$newsItem->setTranslations('hello', $translations);
$newsItem->getTranslations('hello', ['en', 'fr']); // returns ['en' => 'Hello', 'fr' => 'Bonjour']
##Get locales that a model has
You can get all locales that a model has by calling locales() without an argument:

   $translations = ['en' => 'hello', 'es' => 'hola'];
   $newItem->name = $translations;
   $newItem->save();

   $newItem->locales(); // returns ['en', 'es']

Querying translations
If you're using MySQL 5.7 or above, it's recommended that you use the JSON data type for housing translations in the db. This will allow you to query these columns like this:

NewsItem::where('name->en', 'Name in English')->get();
Or if you're using MariaDB 10.2.3 or above :

NewsItem::whereRaw("JSON_EXTRACT(name, '$.en') = 'Name in English'")->get();
If you want to query records based on locales, you can use the whereLocale and whereLocales methods.

NewsItem::whereLocale('name', 'en')->get(); // Returns all news items with a name in English

NewsItem::whereLocales('name', ['en', 'nl'])->get(); // Returns all news items with a name in English or Dutch
If you want to query records based on locale's value, you can use the whereJsonContainsLocale and whereJsonContainsLocales methods.

// Returns all news items that has name in English with value `Name in English` 
NewsItem::query()->whereJsonContainsLocale('name', 'en', 'Name in English')->get(); 

// Returns all news items that has name in English or Dutch with value `Name in English` 
NewsItem::query()->whereJsonContainsLocales('name', ['en', 'nl'], 'Name in English')->get(); 

Customize the toArray method
In many cases, the toArray() method on Model the class is called under the hood to serialize your model.

To customize for all your models what should get returned for the translatable attributes you could wrap theSpatie\Translatable\HasTranslations trait into a custom trait and overrides the toArray() method.

namespace App\Traits;
use Spatie\Translatable\HasTranslations as BaseHasTranslations;

trait HasTranslations
{
    use BaseHasTranslations;

    public function toArray()
    {
        $attributes = $this->attributesToArray(); // attributes selected by the query
        // remove attributes if they are not selected
        $translatables = array_filter($this->getTranslatableAttributes(), function ($key) use ($attributes) {
            return array_key_exists($key, $attributes);
        });
        foreach ($translatables as $field) {
            $attributes[$field] = $this->getTranslation($field, \App::getLocale());
        }
        return array_merge($attributes, $this->relationsToArray());
    }
}