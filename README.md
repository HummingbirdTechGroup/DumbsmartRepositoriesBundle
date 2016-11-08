## Dumbsmart Repositories Bundle

This project is a bridge between [Dumbsmart Repositories](https://github.com/carlosV2/DumbsmartRepositories)
and a symfony project.

[![License](https://poser.pugx.org/carlosv2/dumbsmart-repositories-bundle/license)](https://packagist.org/packages/carlosv2/dumbsmart-repositories-bundle)
[![Build Status](https://travis-ci.org/carlosV2/DumbsmartRepositoriesBundle.svg?branch=master)](https://travis-ci.org/carlosV2/DumbsmartRepositoriesBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4285bc56-fe95-4b16-bb8c-39b9a43f3508/mini.png)](https://insight.sensiolabs.com/projects/4285bc56-fe95-4b16-bb8c-39b9a43f3508)

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require --dev carlosv2/dumbsmart-repositories-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project (as it is meant to be for
development, you may want to place it under your development configuration in
this file):

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new carlosV2\DumbsmartRepositoriesBundle\DumbsmartRepositoriesBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Configure it
--------------------

The following is the available configuration for this bundle:
```
dumbsmart_repositories:
    repositories:
        type: <string>
        path: <string>
    autoconfigure:
        orm: <boolean>
        odm: <boolean>
    entities: <array>
    aliases: <array>
```

Where any value is optional being them:
- dumbsmart_repositories.repositories.type: Repositories type. Available values are `file` or `in_memory`. Default value: `in_memory`.
- dumbsmart_repositories.repositories.path: If repository typ is `file`, this is the folder to create them. Default value: systems temp folder.
- dumbsmart_repositories.autoconfigure.orm: Configure it from doctrine's entities configuration. Default value: `false`.
- dumbsmart_repositories.autoconfigure.odm: Configure it from doctrine's documents configuration. Default value: `false`.
- dumbsmart_repositories.entities: Manual entities configuration. Default value: empty array.
- dumbsmart_repositories.aliases: Set of class aliases for repositories reutilisation. Default value: empty array.

Entities
--------

You may use entities for:
- Entities that cannot be autoconfigured
- Injecting repository on a system that does not support Doctrine

You don't need entities for:
- Objects that you won't need to store in a repository

Each entity must have the following fields:
```
dumbsmart_repositories:
    entities:
        class_1:
            id: id_property
            relations:
                property_1: many
                property_2: one
                ...
        class_2:
            extends: class_1
            relations:
                property_3: one
                property_4: many
                ...
        ...
```

Where:
- id: Points to the ID property in the given class.
- extends: Points to the class that this class is extending from.
- relations: Holds a list of the properties with relations. You only need to set the properties that mapp to other objects
  which you also want to have repositories for. Any object mapped as part of any relation must also have an entry in `entities`.
  Default value: empty array. 

Be aware that a class can only have either `id` or `extends` fields but not both. Also, each property of `relations` can
only have `one` or `many` as assigned values.


Aliases
-------

You may use aliases for:
- Storing together similar classes
- Naming classes after any criteria

You don't need aliases for:
- Inherited classes (if doctrine is set to deal with them, inherited classes will be stored under the same repository).

Depending on the level of configuration you need, you may want to set them using the following shortcut:
```
dumbsmart_repositories:
    aliases:
        alias_class_1: original_class_1
        alias_class_2: original_class_2
        ...
```

Or by using the extended version and aliasing certain fields too (only the ones that change should be defined):
```
dumbsmart_repositories:
    aliases:
        alias_class_1:
            class: original_class_1
            mapping:
                original_class_1_field_A: alias_class_1_field_B
                ...
        alias_class_2:
            class: original_class_2
            mapping:
                original_class_2_field_A: alias_class_2_field_B
                ...
        ...
```

Usage
=====

Once you have completed the configuration, you can request a repository by injecting a service into the dependency
injection as follows:
```
<service id="class.repository"
         class="Everzet\PersistedObjects\Repository">
    <factory service="dumbsmart_repositories.front_repository_factory" method="getRepository" />
    <argument>class</argument>
</service>
```

You can create as many repositories as you want but be aware that, depending on the configuration, the same repository
might be returned for different classes.
