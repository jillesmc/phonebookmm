<?php

namespace Core;


/**
 * Class Validator
 * @package Core
 */
class Validator
{
    /**
     * @param $ruleValue
     * @param $ruleKey
     * @param $dataValue
     * @return array
     */
    private static function simpleRules($ruleValue, $ruleKey, $dataValue)
    {
        $errors = [];
        switch ($ruleValue) {
            case 'required':
                if ($dataValue == '' || empty($dataValue)) {
                    $errors["$ruleKey"] = "O campo {$ruleKey} é requerido";
                }
                break;
            case 'email':
                if ($dataValue!== '' && !filter_var($dataValue, FILTER_VALIDATE_EMAIL)) {
                    $errors["$ruleKey"] = "O campo {$ruleKey} não é válido";
                }
                break;
            case 'phone':
                if (!filter_var($dataValue,
                    FILTER_VALIDATE_REGEXP,
                    ["options" => ["regexp" => "/^[0-9]{2} [0-9]{4,5}-[0-9]{4}$/"]]
                )) {
                    $errors["$ruleKey"] = "O campo {$ruleKey} não é válido";
                }
                break;
            default:
                break;
        }
        return $errors;
    }

    /**
     * @param $ruleValue
     * @param $ruleKey
     * @param $dataValue
     * @return array
     */
    private static function colonRules($ruleValue, $ruleKey, $dataValue)
    {
        $errors = [];
        $item = explode(':', $ruleValue);
        switch ($item[0]) {
            case 'min':
                if (strlen($dataValue) < $item[1]) {
                    $errors["$ruleKey"] = "O campo {$ruleKey} dete ter um mínimo de {$item[1]} caracteres";
                }
                break;
            case 'max':
                if (strlen($dataValue) > $item[1]) {
                    $errors["$ruleKey"] = "O campo {$ruleKey} dete ter um máxmo de {$item[1]} caracteres";
                }
                break;
            case 'unique':
                $model = Container::getModel($item[1]);
                $find = $model->findByField($ruleKey, $dataValue);
                if ($find && $find->{$item[2]}) {
                    if ($item[3] && $find->id == $item[3]) {
                        break;
                    }
                    $errors["$ruleKey"] = "{$ruleKey} já registrado no sistema";
                }
                break;
            default:
                break;
        }

        return $errors;
    }

    /**
     * @param array $data
     * @param array $rules
     * @return array|bool
     */
    public static function make(array $data, array $rules)
    {
        $errors = [];
        foreach ($rules as $ruleKey => $ruleValue) {
            foreach ($data as $dataKey => $dataValue) {
                if ($ruleKey == $dataKey) {
                    if (strpos($ruleValue, '|')) {
                        $itemsValue = explode('|', $ruleValue);
                        foreach ($itemsValue as $itemValue) {
                            if (strpos($itemValue, ':')) {
                                $error = self::colonRules($itemValue, $ruleKey, $dataValue);
                                if (count($error) > 0) {
                                    $errors = array_merge($errors, $error);
                                }
                            } else {
                                $error = self::simpleRules($itemValue, $ruleKey, $dataValue);
                                if (count($error) > 0) {
                                    $errors = array_merge($errors, $error);
                                }
                            }
                        }
                    } elseif (strpos($ruleValue, ':')) {
                        $error = self::colonRules($ruleValue, $ruleKey, $dataValue);
                        if (count($error) > 0) {
                            $errors = array_merge($errors, $error);
                        }
                    } else {

                        $error = self::simpleRules($ruleValue, $ruleKey, $dataValue);
                        if (count($error) > 0) {
                            $errors = array_merge($errors, $error);
                        }
                    }
                }
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }

        return false;
    }

}