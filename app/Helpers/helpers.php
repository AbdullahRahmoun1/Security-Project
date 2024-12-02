<?php

use App\Models\User;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


if(!function_exists('user')){
    function user(){
        $user = request()->user();
        if(!$user instanceof User){
            throwError("custom.wrong_user_type");
        }
        return $user;
    }
}
if(!function_exists('decompressString')){
    function decompressString($compressedString) {
        // Decompress the zlib-compressed bytes
        $originalString = gzuncompress($compressedString);
        return $originalString;
    }
}


if (!function_exists('array_dot')) {
    function array_dot(array $array, $prepend = '')
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, array_dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}



if (!function_exists('throwError')) {
    /**
     * This method just for debug
     */
    function throwError($object,$code = 400): string
    {
        throw new \App\Exceptions\GeneralException(__($object),$code);
    }
}
if (!function_exists('toDate')) {
    /**
     * This method just for debug
     */
    function toDateString($timestamp): string
    {
        return Carbon::parse($timestamp)->toDateString();
    }
}
if (!function_exists('hourOnly')) {
    /**
     * This method just for debug
     */
    function hourOnly($date): string
    {
        return Carbon::parse($date)->format('h:i A');
    }
}
if (!function_exists('getEnumValueRegexRule')) {
    /**
     * This method just for debug
     */
    function getEnumValueRegexRule($enum): string
    {
        $regex=implode('|',getEnumValues($enum));
        return "regex:/^($regex)$/";
    }
}

if (!function_exists('changeLang')) {
    function changeLang(string $lang = 'en'): void
    {
        App::setLocale($lang);
    }
}

if (!function_exists('snakeCase')) {
    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @return string
     */
    function snakeCase($value)
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $value));
        }
        return $value;
    }
}

if (!function_exists('kebabCase')) {
    /**
     * Convert a string to kebab case.
     *
     * @param string $value
     * @return string
     */
    function kebabCase($value)
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1-', $value));
        }
        return $value;
    }
}

if (!function_exists('pascalCase')) {
    /**
     * Convert a string to Pascal case.
     *
     * @param string $value
     * @return string
     */
    function pascalCase($value)
    {
        $value = snakeCase($value);
        $value = str_replace(' ', '', ucwords(str_replace('_', ' ', $value)));

        return $value;
    }
}

if (!function_exists('camelCase')) {
    /**
     * Convert a string to camel case.
     *
     * @param string $value
     * @return string
     */
    function camelCase($value)
    {
        $value = pascalCase($value);
        $value = lcfirst($value);

        return $value;
    }
}

if (!function_exists('pascalCaseWithSpaces')) {
    /**
     * Convert a snake_case string to Pascal case with spaces.
     *
     * @param string $value
     * @return string
     */
    function pascalCaseWithSpaces($value)
    {
        return preg_replace('/([a-z])([A-Z])/', '$1 $2', pascalCase($value));
    }
}

if (!function_exists('getEnumValues')) {
    function getEnumValues($enum): array
    {
        return enum_exists($enum)
            ? array_column($enum::cases(), 'value')
            : [];
    }
}

if (!function_exists('storeFile')) {
    function storeFile($path, $file)
    {
        return 'storage/' . Storage::disk('public')->put($path, $file);
    }
}
if (!function_exists('greaterDate')) {
    /**
     * Return the greater of two dates.
     *
     * @param string $date1
     * @param string $date2
     * @return string
     */
    function greaterDate($date1, $date2)
    {
        $carbonDate1 = Carbon::parse($date1);
        $carbonDate2 = Carbon::parse($date2);

        return $carbonDate1->greaterThan($carbonDate2) ? $date1 : $date2;
    }
}
if (!function_exists('diffDateByYear')) {
    function diffDateByYear($from_date, $to_date)
    {
        return Carbon::parse($from_date)->diff($to_date)->y;
    }
}
if (!function_exists('diffDateByMonth')) {
    function diffDateByMonth($from_date, $to_date)
    {
        return Carbon::parse($from_date)->diff($to_date)->m;
    }
}

if (!function_exists('diffDateByDay')) {
    function diffDateByDay($from_date, $to_date)
    {
        return Carbon::parse($from_date)->diff($to_date)->days;
    }
}

if (!function_exists('getArg')) {
    function getArg(string $key, mixed ...$args): mixed
    {
        $args = $args[0];

        foreach ($args as $arg) {
            if (is_array($arg) && isset($arg[$key])) {
                return $arg[$key];
            }
        }

        return null;
    }
}

