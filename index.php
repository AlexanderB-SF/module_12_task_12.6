<?php
// Исходный массив 
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];

//---------------------------------------------------------------------------------------------------------
// Объединение ФИО
// результат функции, присвоенна переменной, для упрощения записи
$fioParts = getPartsFromFullname($example_persons_array[0]['fullname']);
function getFullnameFromParts ($surname, $name, $patronymic) {
    // Склейка конкатенецией строк из массива
    $fullNameFromParts = $surname . " " . $name . " " .  $patronymic;
    return $fullNameFromParts;
}
echo "Объединение ФИО: " . (getFullnameFromParts($fioParts['surname'], $fioParts['name'], $fioParts['patronymic'])) . "<br>";
echo '________________________________________<br><br>';



//---------------------------------------------------------------------------------------------------------
// Разбиение ФИО
function getPartsFromFullname ($strFull) {
    // Разбивка строки ФИО на 3 части и запись в массив
    $fullnameStr = explode(" ", $strFull);
    // Замена числовых индексов в массиве на ассоциативные
    $partsFromFullname['surname'] = $fullnameStr[0];
    $partsFromFullname['name'] = $fullnameStr[1];
    $partsFromFullname['patronymic'] = $fullnameStr[2];
    return $partsFromFullname;
}
echo "Разбиение ФИО: ";
echo "<pre>";
print_r(getPartsFromFullname($example_persons_array[0]['fullname']));
echo "</pre>";
echo '________________________________________<br><br>';


//---------------------------------------------------------------------------------------------------------
// Сокращение ФИО
function getShortName($strFullName) {
    // Разбтение строки на части
    $buffPartsName = getPartsFromFullname($strFullName);
    // конкатенация строк и вырезается 1я буква фамилии 
    $nameShort = $buffPartsName['name'] . " " . mb_substr($buffPartsName['surname'], 0, 1) . ".";
    return mb_convert_case($nameShort, MB_CASE_TITLE_SIMPLE);
}
echo "Сокращение ФИО: " . getShortName($example_persons_array[0]['fullname']) . "<br>";
echo '________________________________________<br><br>';


//---------------------------------------------------------------------------------------------------------
// Функция определения пола по ФИО
function getGenderFromName ($fullName) {
    $buffPartsName = getPartsFromFullname($fullName);

    // изначально «суммарный признак пола» считаем равным 0
    $summaryOfSex = 0;

    // проверка фио
    if (mb_substr($buffPartsName['surname'], -2) === "ва") {
        $summaryOfSex--;
    } elseif (mb_substr($buffPartsName['surname'], -1) === "в"){
        $summaryOfSex++;
    }

    if (mb_substr($buffPartsName['name'], -1) === "а") {
        $summaryOfSex--;
    } elseif (mb_substr($buffPartsName['name'], -1) === "н" || mb_substr($buffPartsName['name'], -1) === "й" ){
        $summaryOfSex++;
    }

    if (mb_substr($buffPartsName['patronymic'], -3) === "вна") {
        $summaryOfSex--;
    } elseif (mb_substr($buffPartsName['patronymic'], -2) === "ич"){
        $summaryOfSex++;
    }

    // Определение пола исходя из «суммарный признак пола» полученного в ходе проверки фио
    if ($summaryOfSex < 0) {
        $gender = 'Женский';
    } elseif ($summaryOfSex > 0) {
        $gender = 'Мужской';
    } else {
        $gender = 'Неопределенный';
    }
    return $gender;
}
echo "Функция определения пола по ФИО: " . getGenderFromName(getFullnameFromParts($fioParts['surname'], $fioParts['name'], $fioParts['patronymic'])) . "<br>";
echo '________________________________________<br><br>';


//---------------------------------------------------------------------------------------------------------
// Определение возрастно-полового состава
function getGenderDescription ($arrPerson) {
    // Получение массива с гендерными признаками которое возвращает функция getGenderFromName
    foreach ($arrPerson as $key => $value) {
        $genderMass[] = ['fullname' => $value['fullname'], 'gender' => getGenderFromName($value['fullname'])];
    }

    // Определение количества с помощью функции  фильтра и подсчета элементов массива
    $countPerson = count($genderMass);
    $maleGender = count(array_filter($genderMass, function($genderMassItem) {
        return ($genderMassItem['gender'] === 'Мужской');
    }));
    $femaleGender = count(array_filter($genderMass, function($genderMassItem) {
        return ($genderMassItem['gender'] === 'Женский');
    }));
    $nonameGender = count(array_filter($genderMass, function($genderMassItem) {
        return ($genderMassItem['gender'] === 'Неопределенный');
    }));

    // Определение каждого полового признака в %, от общего кол-ва аудитории
    $maleGender = round(($maleGender / $countPerson) * 100, 1);
    $femaleGender = round(($femaleGender / $countPerson) * 100, 1);
    $nonameGender = round(($nonameGender / $countPerson) * 100, 1);

    // Вывод на страницу данных
    echo 'Гендерный состав аудитории:<br>';
    echo '-----------------------------------------<br>';
    echo "Мужчины - $maleGender%<br>";
    echo "Женщины - $femaleGender%<br>";
    echo "Не удалось определить - $nonameGender% <br>";
}
getGenderDescription($example_persons_array);
echo '________________________________________<br><br>';


//---------------------------------------------------------------------------------------------------------
// Идеальный подбор пары
function getPerfectPartner ($surname, $name, $patronymic, $arr) {
    // Преобразование строк к одному регистру
    $surname = mb_convert_case($surname, MB_CASE_LOWER_SIMPLE);
    $name = mb_convert_case($name, MB_CASE_LOWER_SIMPLE);
    $patronymic = mb_convert_case($patronymic, MB_CASE_LOWER_SIMPLE);

    // % рандомное число совместимости от 50% - 100%
    $compatibility = rand(5000, 10000) / 100;

    // Получение полного фио
    $fullName = getFullnameFromParts($surname, $name, $patronymic);

    // Получение пола
    $genderPers = getGenderFromName($fullName);

    // В цикле случайным образом выбираем любого человека в массиве
    do {
        $randomnNumPerson = rand(0, count($arr) - 1);
        $randomnPers = $arr[$randomnNumPerson]['fullname'];
        $genderRandPers = getGenderFromName($randomnPers);
    // Проверка на совместимость пола
    } while ($genderPers === $genderRandPers || $genderRandPers === 'Неопределенный');

    // Вывод на странницу результат вычисления функции
    echo getShortName($fullName) . " + " . getShortName($randomnPers) . " = ♡ Идеально на " . $compatibility . "% ♡";
}
echo "Идеальный подбор пары: <br><br>";
getPerfectPartner($fioParts['surname'], $fioParts['name'], $fioParts['patronymic'], $example_persons_array);

?>
