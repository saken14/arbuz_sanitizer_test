<?php

class Sanitizer
{
    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @throws Exception
     */
    public function sanitize($data): array
    {
        $sanitizedData = [];
        $errors        = [];

        // итерируемся по правилам
        foreach ($this->rules as $key => $type) {
            if (!is_array($type)) {
                throw new Exception('List of types must be in an array');
            }
            // если нет в нашем массиве, записываем ошибку в $errors
            if (!isset($data[ $key ])) {
                $errors[ $key ] = "Field '$key' is missing.";
                continue;
            }

            // берем значение по индексу, для дальнейшей проверки
            $value = $data[ $key ];

            // проверка на тип
            switch ($type[ 0 ]) {
                case 'string':
                    if (is_string($value)) {
                        // записываем в массив пройденных проверку
                        $sanitizedData[ $key ] = $value;
                    } else {
                        // иначе записываем ошибку
                        $errors[ $key ] = "Field '$key' should be a string.";
                    }
                    break;

                case 'integer':
                    // проверка на цифру и на целое число
                    if (is_numeric($value) && is_int($value + 0)) {
                        $sanitizedData[ $key ] = (int)$value;
                    } else {
                        $errors[ $key ] = "Field '$key' should be an integer.";
                    }
                    break;

                case 'float':
                    // проверка на цифру
                    if (is_numeric($value)) {
                        $sanitizedData[ $key ] = (float)$value;
                    } else {
                        $errors[ $key ] = "Field '$key' should be a float.";
                    }
                    break;

                case 'phone_number_kz':
                    // 8747...
                    // 7747...
                    $phoneNumber = preg_replace('/[^0-9]/', '', $value);
                    if (strlen($phoneNumber) === 11 && preg_match('/^[7|8]7[0-9]*/', $phoneNumber)) {
                        $firstDigit = substr($phoneNumber, 0, 1);
                        if ($firstDigit === '8') {
                            $phoneNumber = '7' . substr($phoneNumber, 1);
                        }
                        $sanitizedData[ $key ] = $phoneNumber;
                    } else {
                        $errors[ $key ] = "Field '$key' should be a valid Kazakhstan phone number.";
                    }
                    break;

                case 'one_type_array':
                    // Массив с элементами одного фиксированного поддерживаемого типа
                    if (is_array($value)) {
                        $typeFirst = gettype(reset($value));
                        foreach ($value as $item) {
                            if (gettype($item) !== $typeFirst) {
                                $errors[ $key ] = "Field '$key' should be an one typed array";
                                // выходим сразу с двух (foreach и case)
                                break 2;
                            }
                        }
                        $sanitizedData[ $key ] = $value;
                    } else {
                        $errors[ $key ] = "Field '$key' should be an array";
                    }
                    break;

                case 'structure':
                    // Структура (ассоциативный массив с заранее известными ключами)

                    // если есть известные ключи
                    if (isset($type[ 1 ])) {
                        if (is_array($type[ 1 ])) {
                            foreach ($type[ 1 ] as $knownKey) {
                                if (!array_key_exists($knownKey, $value)) {
                                    $errors[ $key ] = "Field '$key' should have '$knownKey' field";
                                    // выходим сразу с двух (foreach и case)
                                    break 2;
                                }
                            }
                            $sanitizedData[ $key ] = $value;
                        } else {
                            throw new Exception('List of given fields must be in an array');
                        }
                    } else {
                        throw new Exception('No given fields of structure');
                    }


                    if (is_array($value)) {
                        $typeFirst = gettype(reset($value));
                        foreach ($value as $item) {
                            if (gettype($item) !== $typeFirst) {
                                $errors[ $key ] = "Field '$key' should be an one typed array";
                                // выходим сразу с двух (foreach и case)
                                break 2;
                            }
                        }
                        $sanitizedData[ $key ] = $value;
                    } else {
                        $errors[ $key ] = "Field '$key' should be an structure";
                    }
                    break;

                default:
                    // проверка, что функция
                    if (is_callable($type[ 1 ])) {
                        $isValid = call_user_func($type[ 1 ], $value);
                        if ($isValid) {
                            $sanitizedData[ $key ] = $value;
                        } else {
                            $errors[ $key ] = "Field '$key' should be a type of '$type[0]'";
                        }
                    } else {
                        throw new Exception('Second argument must be a callable rule function');
                    }

                    break;
            }
        }

        return [ 'data' => $sanitizedData, 'errors' => $errors ];
    }
}
