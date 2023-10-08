> ! Все запросы и ответы были протестированы с помощью Postman.


## Данные для отправки запроса

```json
{
    "foo": "123",
    "bar": "asd",
    "baz": "8 (707) 288-56-23",
    "one_type_arr": [
        "1",
        "2",
        "3",
        "4"
    ],
    "it_is_struct": {
        "name": "Toyota",
        "model": "Camry",
        "color": "White",
        "born": "2017"
    },
    "for_add": "no"
}
```

## Ответ:

```json
{
    "success": true,
    "data": {
        "foo": 123,
        "bar": "asd",
        "baz": "77072885623",
        "one_type_arr": [
            "1",
            "2",
            "3",
            "4"
        ],
        "it_is_struct": {
            "name": "Toyota",
            "model": "Camry",
            "color": "White",
            "born": "2017"
        },
        "for_add": "no"
    }
}
```


## Запрос №2

```json
{
    "foo": "123",
    "bar": "asd",
    "baz": "8 (707) 288-56-2",
    "one_type_arr": [
        "1",
        2,
        "3",
        "4"
    ],
    "it_is_struct": {
        "name": "Toyota",
        "model": "Camry",
        "color": "White",
        "born": "2017"
    },
    "for_add": "noo"
}
```

## Ответ №2:

```json
{
    "success": false,
    "errors": {
        "baz": "Field 'baz' should be a valid Kazakhstan phone number.",
        "one_type_arr": "Field 'one_type_arr' should be an one typed array",
        "for_add": "Field 'for_add' should be a type of 'my_bool'"
    }
}
```

## Запрос №3

```json
{
    "foo": "123",
    "bar": "asd",
    "baz": "8 (707) 288-56-23",
    "one_type_arr": [
        "1",
        "2",
        "3",
        "4"
    ],
    "it_is_struct": {
        "name": "Toyota",
        "color": "White",
        "born": "2017"
    },
    "for_add": "no"
}
```

## Ответ #3:

```json
{
    "success": false,
    "errors": {
        "it_is_struct": "Field 'it_is_struct' should have 'model' field"
    }
}
```
