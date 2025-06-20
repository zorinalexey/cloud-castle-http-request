# Руководство для контрибьюторов

## Установка для разработки

1. Клонируйте репозиторий:
```bash
git clone https://github.com/zorinalexey/cloud-castle-http-request
cd http-request
```

2. Установите зависимости:
```bash
composer install
```

## Запуск тестов

```bash
composer test
```

## Проверка стиля кода

```bash
composer cs-check
```

## Отправка изменений

1. Создайте новую ветку для ваших изменений
2. Внесите изменения
3. Напишите тесты
4. Убедитесь, что все тесты проходят
5. Отправьте pull request

## Требования к коду

- Следуйте PSR-12
- Пишите тесты для нового функционала
- Обновляйте документацию
- Используйте типизацию 