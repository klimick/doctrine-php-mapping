All examples borrowed from [original Doctrine documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/association-mapping.html).

- [Many-To-One, Unidirectional](associations-example.md#many-to-one-unidirectional)
- [One-To-One, Unidirectional](associations-example.md#one-to-one-unidirectional)
- [One-To-One, Bidirectional](associations-example.md#one-to-one-bidirectional)
- [One-To-One, Self-referencing](associations-example.md#one-to-one-self-referencing)
- [One-To-Many, Bidirectional](associations-example.md#one-to-many-bidirectional)
- [One-To-Many, Unidirectional with Join Table](associations-example.md#one-to-many-unidirectional-with-join-table)
- [One-To-Many, Self-referencing](associations-example.md#one-to-many-self-referencing)
- [Many-To-Many, Unidirectional](associations-example.md#many-to-many-unidirectional)
- [Many-To-Many, Bidirectional](associations-example.md#many-to-many-bidirectional)
- [Many-To-Many, Self-referencing](associations-example.md#many-to-many-self-referencing)

## Many-To-One, Unidirectional

Many Users have One Address:

```php
class User
{
    public function __construct(
        private string $id,
        private Address $address,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}

class Address
{
    public function __construct(
        private string $id,
        private string $value,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
```

```php
/**
 * @extends EntityMapping<User>
 */
final class UserMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return User::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
        ];
    }

    public static function manyToOne(): array
    {
        return [
            'address' => manyToOne(AddressMapping::class),
        ];
    }
}

/**
 * @extends EntityMapping<Address>
 */
final class AddressMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return Address::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'value' => field(StringType::class),
        ];
    }
}
```

## One-To-One, Unidirectional

One Product have One Shipment:

```php
class Product
{
    public function __construct(
        private string $id,
        private Shipment $shipment,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getShipment(): Shipment
    {
        return $this->shipment;
    }
}

class Shipment
{
    public function __construct(private string $id)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
```

```php
/**
 * @extends EntityMapping<Shipment>
 */
final class ShipmentMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return Shipment::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
        ];
    }
}

/**
 * @extends EntityMapping<Product>
 */
final class ProductMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return User::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
        ];
    }

    public static function oneToOne(): array
    {
        return [
            'shipment' => oneToOne(ShipmentMapping::class),
        ];
    }
}
```

## One-To-One, Bidirectional

One Customer have one Cart and Cart has a reference back to the Customer:

```php
class Customer
{
    private Cart $cart;

    public function __construct(private string $id)
    {
        $this->cart = new Cart(id: 'cart-id', quantity: 0, customer: $this);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }
}

class Cart
{
    public function __construct(
        private string $id,
        private int $quantity,
        private Customer $customer,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}
```

```php
/**
 * @extends EntityMapping<Customer>
 */
final class CustomerMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return Customer::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
        ];
    }

    public static function oneToOne(): array
    {
        return [
            'cart' => owningSide(inversedBy: 'customer')
                ->oneToOne(CartMapping::class)
        ];
    }
}

/**
 * @extends EntityMapping<Cart>
 */
final class CartMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return Cart::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'quantity' => field(IntegerType::class),
        ];
    }

    public static function oneToOne(): array
    {
        return [
            'customer' => inverseSide(mappedBy: 'cart')
                ->oneToOne(CustomerMapping::class)
        ];
    }
}
```

## One-To-One, Self-referencing

Student have one Mentor (Mentor is Student too):

```php
class Student
{
    private null|Student $mentor = null;

    public function __construct(private string $id)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMentor(): ?Student
    {
        return $this->mentor;
    }

    public function setMentor(?Student $mentor): void
    {
        $this->mentor = $mentor;
    }
}
```

```php
/**
 * @extends EntityMapping<Student>
 */
final class StudentMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return Student::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
        ];
    }

    public static function oneToOne(): array
    {
        return [
            'mentor' => oneToOne(StudentMapping::class)->nullable(),
        ];
    }
}
```

## One-To-Many, Bidirectional

One Product have many Features:

```php
class Product
{
    /** @var Collection<int, Feature> */
    private Collection $features;

    public function __construct(private string $id)
    {
        $this->features = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array<int, Feature>
     */
    public function getFeatures(): array
    {
        return $this->features->toArray();
    }

    public function addFeature(string $featureId, string $name): Feature
    {
        $feature = new Feature($featureId, $name, $this);
        $this->features->add($feature);

        return $feature;
    }

    public function removeFeature(Feature $feature): void
    {
        $this->features->removeElement($feature);
    }
}

class Feature
{
    public function __construct(
        private string $id,
        private string $name,
        private Product $product,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
```

```php
/**
 * @extends EntityMapping<Product>
 */
final class ProductMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return Product::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
        ];
    }

    public static function oneToMany(): array
    {
        return [
            'features' => inverseSide(mappedBy: 'product')
                ->oneToMany(FeatureMapping::class),
        ];
    }
}

/**
 * @extends EntityMapping<Feature>
 */
final class FeatureMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return Feature::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'name' => field(StringType::class),
        ];
    }

    public static function manyToOne(): array
    {
        return [
            'product' => owningSide(inversedBy: 'features')
                ->manyToOne(ProductMapping::class),
        ];
    }
}
```

## One-To-Many, Unidirectional with Join Table

One User have many Phonenumbers:

```php
class User
{
    /** @var Collection<int, Phonenumber> */
    private Collection $phonenumbers;

    public function __construct(private string $id, private string $name)
    {
        $this->phonenumbers = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Phonenumber>
     */
    public function getPhonenumbers(): array
    {
        return $this->phonenumbers->toArray();
    }

    public function addPhonenumber(Phonenumber $phonenumber): void
    {
        $this->phonenumbers->add($phonenumber);
    }

    public function removePhonenumber(Phonenumber $phonenumber): void
    {
        $this->phonenumbers->removeElement($phonenumber);
    }
}

class Phonenumber
{
    public function __construct(private string $id, private string $value)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
```

```php
/**
 * @extends EntityMapping<User>
 */
final class UserMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return User::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'name' => field(StringType::class),
        ];
    }

    public static function manyToMany(): array
    {
        return [
            'phonenumbers' => manyToMany(PhonenumberMapping::class)
                ->joinTable(
                    name: 'user_phonenumbers',
                    joinColumns: [
                        joinColumn(name: 'user_id', referencedColumnName: 'id'),
                    ],
                    inverseJoinColumns: [
                        joinColumn(name: 'phonenumber_id', referencedColumnName: 'id', unique: true),
                    ],
                ),
        ];
    }
}

/**
 * @extends EntityMapping<Phonenumber>
 */
final class PhonenumberMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return Phonenumber::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'value' => field(StringType::class),
        ];
    }
}
```

## One-To-Many, Self-referencing

```php

One Category have many Categories:

class Category
{
    /** @var Collection<int, Category> */
    private Collection $children;
    private ?Category $parent = null;

    public function __construct(private string $id, private string $name)
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent): void
    {
        $this->parent?->children->removeElement($this);
        $parent?->children->add($this);

        $this->parent = $parent;
    }

    /**
     * @return array<int, Category>
     */
    public function getChildren(): array
    {
        return $this->children->toArray();
    }
}
```

```php
/**
 * @extends EntityMapping<Category>
 */
final class CategoryMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return Category::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'name' => field(StringType::class),
        ];
    }

    public static function manyToOne(): array
    {
        return [
            'parent' => owningSide(inversedBy: 'children')
                ->manyToOne(CategoryMapping::class)
                ->nullable(),
        ];
    }

    public static function oneToMany(): array
    {
        return [
            'children' => inverseSide(mappedBy: 'parent')
                ->oneToMany(CategoryMapping::class),
        ];
    }
}
```

## Many-To-Many, Unidirectional

Many Users have many UserGroups:

```php
class User
{
    /** @var Collection<int, UserGroup> */
    private Collection $groups;

    public function __construct(private string $id, private string $login)
    {
        $this->groups = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return array<int, UserGroup>
     */
    public function getGroups(): array
    {
        return $this->groups->toArray();
    }

    public function attachGroup(UserGroup $group): void
    {
        $this->groups->add($group);
    }

    public function detachGroup(UserGroup $group): void
    {
        $this->groups->removeElement($group);
    }
}

class UserGroup
{
    public function __construct(private string $id, private string $name)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
```

```php
/**
 * @extends EntityMapping<User>
 */
final class UserMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return User::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'login' => field(StringType::class),
        ];
    }

    public static function manyToMany(): array
    {
        return [
            'groups' => manyToMany(UserGroupMapping::class),
        ];
    }
}

/**
 * @extends EntityMapping<UserGroup>
 */
final class UserGroupMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return UserGroup::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'name' => field(StringType::class),
        ];
    }
}
```

## Many-To-Many, Bidirectional

Many Users have many UserGroups and UserGroup has a reference back to the many Users:

```php
class User
{
    /** @var Collection<int, UserGroup> */
    private Collection $groups;

    public function __construct(private string $id, private string $login)
    {
        $this->groups = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return array<int, UserGroup>
     */
    public function getGroups(): array
    {
        return $this->groups->toArray();
    }

    public function attachGroup(UserGroup $group): void
    {
        $this->groups->add($group);
        $group->attachUser($this);
    }

    public function detachGroup(UserGroup $group): void
    {
        $this->groups->removeElement($group);
        $group->detachUser($this);
    }
}

class UserGroup
{
    /** @var Collection<int, User> */
    private Collection $users;

    public function __construct(private string $id, private string $name)
    {
        $this->users = new ArrayCollection();
    }

    public function attachUser(User $user): void
    {
        $this->users->add($user);
    }

    public function detachUser(User $user): void
    {
        $this->users->removeElement($user);
    }

    /**
     * @return array<int, User>
     */
    public function getUsers(): array
    {
        return $this->users->toArray();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
```

```php
/**
 * @extends EntityMapping<User>
 */
final class UserMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return User::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'login' => field(StringType::class),
        ];
    }

    public static function manyToMany(): array
    {
        return [
            'groups' => owningSide(inversedBy: 'users')
                ->manyToMany(UserGroupMapping::class)
                ->joinTable(
                    name: 'user_groups',
                    joinColumns: [
                        joinColumn(name: 'user_id', referencedColumnName: 'id')
                    ],
                    inverseJoinColumns: [
                        joinColumn(name: 'group_id', referencedColumnName: 'id')
                    ],
                ),
        ];
    }
}

/**
 * @extends EntityMapping<UserGroup>
 */
final class UserGroupMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return UserGroup::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'name' => field(StringType::class),
        ];
    }

    public static function manyToMany(): array
    {
        return [
            'users' => inverseSide(mappedBy: 'groups')
                ->manyToMany(UserMapping::class),
        ];
    }
}
```

## Many-To-Many, Self-referencing

Many Users have Many Users:

```php
class User
{
    /** @var Collection<int, User> */
    private Collection $friendsWithMe;

    /** @var Collection<int, User> */
    private Collection $friends;

    public function __construct(private string $id, private string $name)
    {
        $this->friendsWithMe = new ArrayCollection();
        $this->friends = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addFriend(User $user): void
    {
        $this->friends->add($user);
    }

    public function addFriendsWithMe(User $user): void
    {
        $user->friends->add($this);
        $this->friendsWithMe->add($user);
    }

    /**
     * @return array<int, User>
     */
    public function getFriendsWithMe(): array
    {
        return $this->friendsWithMe->toArray();
    }

    /**
     * @return array<int, User>
     */
    public function getFriends(): array
    {
        return $this->friends->toArray();
    }
}

/**
 * @extends EntityMapping<User>
 */
final class UserMapping extends EntityMapping
{
    public static function forClass(): string
    {
        return User::class;
    }

    public static function fields(): array
    {
        return [
            'id' => id(StringType::class),
            'name' => field(StringType::class),
        ];
    }

    public static function manyToMany(): array
    {
        return [
            'friendsWithMe' => inverseSide(mappedBy: 'friends')
                ->manyToMany(UserMapping::class),
            'friends' => owningSide(inversedBy: 'friendsWithMe')
                ->manyToMany(UserMapping::class)
                ->joinTable(
                    name: 'friends',
                    joinColumns: [
                        joinColumn(name: 'user_id', referencedColumnName: 'id'),
                    ],
                    inverseJoinColumns: [
                        joinColumn(name: 'friend_user_id', referencedColumnName: 'id'),
                    ],
                ),
        ];
    }
}
```