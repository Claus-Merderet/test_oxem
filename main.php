<?php
class Farm
{
   public $animal_list = [], $product = [];

   public function add_animal(array $animal_list)
   {
      foreach ($animal_list as $name => $amount) {
         $lowercase_name = strtolower($name);
         $class_name = ucfirst($lowercase_name);
         for ($i = 0; $i < $amount; $i++) {
            $this->animal_list[$lowercase_name][] = new $class_name();
         }
      }
      foreach ($this->animal_list as $name => $obj) {
         foreach ($name::PRODUCT as $type_product) {
            if (isset($this->product["$type_product"]) == 0) {
               $this->product["$type_product"] = 0;
            }
         }
      }
   }

   public function collect_products()
   {
      $final  = $this->product;
      $n = 0;
      $basket = [];
      foreach ($this->animal_list as $animals => $key) {

         $basket_products = [];
         foreach ($key as $animal) {
            foreach ($animal->get_product() as $amount_products) {
               array_push($basket_products, $amount_products);
            }
         }
         for ($i = count($key) * count($animals::PRODUCT) - 1; $i >= count($animals::PRODUCT); $i--) {
            $basket_products[$i - count($animals::PRODUCT)] += $basket_products[$i];
            unset($basket_products[$i]);
         }
         $basket_products =  array_combine($animals::PRODUCT, $basket_products);
         $basket[$n] = $basket_products;
         $n++;
      }
      foreach ($basket as $subArray) {
         foreach ($subArray as $val => $key) {
            $newArray[$val] = $key;
         }
      }
      array_walk_recursive($newArray, function ($item, $key) use (&$final) {
         $final[$key] = !isset($final[$key]) ?  $item + $final[$key] : $item;
      });
      return $final;
   }

   public function collect_products_continuous($count = 1)
   {
      for ($i = 0; $i < $count; $i++) {
         $final = $this->collect_products();
         foreach ($this->animal_list as $name => $obj) {
            foreach ($name::PRODUCT as $type_product) {
               $this->product["$type_product"] += $final["$type_product"];
            }
         }
      }
   }
   public function get_product()
   {
      echo '<br/> Collected products : <br/>';
      foreach ($this->product as $name => $key) {
         echo $name . ': ' . $key . '<br/>';
      }
   }

   public function get_animal()
   {
      foreach ($this->animal_list as $name) {
         foreach ($name as $key => $type) {
            echo get_class($type) . ": " .
               $name[$key]->animal_id . "<br/>";
         }
      }
   }

   public function get_info()
   {
      echo '<br/>Amount of animals on the farm: <br/>';
      foreach (array_keys($this->animal_list) as $name) {
         echo $name . ': ' . count($this->animal_list["$name"]) . '<br/>';
      }
   }
}

abstract class Animal
{
   public $animal_id;

   public function __construct()
   {
      $this->animal_id = uniqid("type", true);
   }

   abstract public function get_product();
}

class Chicken extends Animal
{
   public const PRODUCT = ["Eggs", "Fluff"];
   private const MIN_EGGS = 0;
   private const MAX_EGGS = 1;

   public function get_product()
   {
      return [random_int(self::MIN_EGGS, self::MAX_EGGS), random_int(2, 3)];
   }
}

class Cow extends Animal
{
   public const PRODUCT = ["Milk", "Meat"];
   private const MIN_MILK = 8;
   private const MAX_MILK = 12;

   public function get_product()
   {
      return [random_int(self::MIN_MILK, self::MAX_MILK), random_int(99, 100)];
   }
}

$farm_obj = new Farm;
$farm_obj->add_animal([
   'cow' => 10,
   'chicken' => 20
]);
//$farm_obj->get_animal();// информация о всех животных на ферме
$farm_obj->get_info();
$farm_obj->collect_products_continuous(7);
$farm_obj->get_product();
$farm_obj->add_animal([
   'cow' => 1,
   'chicken' => 5
]);
$farm_obj->get_info();
$farm_obj->collect_products_continuous(7);
$farm_obj->get_product();
