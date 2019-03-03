# Plexengine

Документация будет... После альфы...

## Пространства имён

```text
pe\engine  - Системные классы
pe\modules - Пространство имён модулей
```

## Модули

Главный файл модуля должен находиться по пути `/data/modules/<НазваниеМодуля>/Main.php`
и содержать класс `Main` в пространстве имён `pe\modules\<НазваниеМодуля>`.

Пример
```php
// Файл: /data/modules/MyModule/Main.php
namespace pe\modules\MyModule;
class Main {
    ...
}
```
