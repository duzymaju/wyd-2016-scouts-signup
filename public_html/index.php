<?php

error_reporting(E_ALL);

$db = new PDO('mysql:host=127.0.0.1;dbname=sdm', 'root', '', [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
]);

$statement = $db->query('SHOW TABLES LIKE "sdm_volunteers"');
if (!$statement->fetch(PDO::FETCH_ASSOC)) {
    $statement = $db->query(
        'CREATE TABLE IF NOT EXISTS `sdm_volunteers` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `first_name` varchar(50) COLLATE utf8_polish_ci NOT NULL,
            `last_name` varchar(50) COLLATE utf8_polish_ci NOT NULL,
            `grade_id` tinyint(3) unsigned NOT NULL,
            `region_id` tinyint(3) unsigned NOT NULL,
            `pesel` bigint(20) unsigned NOT NULL,
            `address` varchar(255) COLLATE utf8_polish_ci NOT NULL,
            `service_id` tinyint(3) unsigned NOT NULL,
            `permissions` varchar(255) COLLATE utf8_polish_ci NOT NULL,
            `languages` varchar(255) COLLATE utf8_polish_ci NOT NULL,
            `profession` varchar(255) COLLATE utf8_polish_ci NOT NULL,
            `phone` varchar(15) COLLATE utf8_polish_ci NOT NULL,
            `mail` varchar(40) COLLATE utf8_polish_ci NOT NULL,
            `service_time` varchar(255) COLLATE utf8_polish_ci NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `pesel` (`pesel`),
            UNIQUE KEY `mail` (`mail`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1;'
    );
}

$grades = [
    0 => 'brak',
    1 => 'przewodnik/przewodniczka',
    2 => 'podharcmistrz/podharcmistrzyni',
    3 => 'harcmistrz/harcmistrzyni',
];
$regions = [
    1 => 'Chorągiew Białostocka',
    2 => 'Chorągiew Dolnośląska',
    3 => 'Chorągiew Gdańska',
    4 => 'Chorągiew Kielecka',
    5 => 'Chorągiew Krakowska',
    6 => 'Chorągiew Kujawsko-Pomorska',
    7 => 'Chorągiew Lubelska',
    8 => 'Chorągiew Łódzka',
    9 => 'Chorągiew Mazowiecka',
    10 => 'Chorągiew Opolska',
    11 => 'Chorągiew Podkarpacka',
    12 => 'Chorągiew Stołeczna',
    13 => 'Chorągiew Śląska',
    14 => 'Chorągiew Warmińsko-Mazurska',
    15 => 'Chorągiew Wielkopolska',
    16 => 'Chorągiew Zachodniopomorska',
    17 => 'Chorągiew Ziemi Lubuskiej',
];
$services = [
    1 => [
        'name' => 'medyczna',
        'desc' => 'kwalifikacje minimum nasz ZHP-owski kurs wędrowniczy ale najlepiej KPP',
    ],
    2 => [
        'name' => 'porządkowa',
        'desc' => 'ochrona miasteczek, porządki w miasteczkach, a także pkt. spotkań pielgrzymów w różnych częściach ' .
            'Krakowa, kierowanie ruchem, zarządzanie parkingami',
    ],
    3 => [
        'name' => 'kwatermistrzowska',
        'desc' => 'budowa miasteczek, zarządzanie miasteczkami',
    ],
    4 => [
        'name' => 'informacyjna',
        'desc' => 'udzielanie informacji na dworcach PKP, PKS, lotnisku, centach spotkań pielgrzymów, miasteczkach ' .
            'noclegowych, punktach na autostradach, drogach dojazdowych, kierowanie autokarów na odpowiednie parkingi',
    ],
    5 => [
        'name' => 'programowa',
        'desc' => 'organizacja programu w miasteczkach, koncerty, katechezy wieczorne, a takze całodniowe propozycje ' .
            'programowe, wycieczki itp.',
    ],
    6 => [
        'name' => 'biuro miasteczka',
        'desc' => 'rejstracja pielgrzymów, zakwaterowanie, propozycje programowe - mile widziane języki obce',
    ],
    7 => [
        'name' => 'kuchenna',
        'desc' => 'wydawanie śniadań i pakietów na drugie śniadanie, praca w regionalnych restauracjach ' .
            'zlokalizowanych na terenie miasteczek',
    ],
];

function escape($text, $flags = ENT_QUOTES) {
    return htmlspecialchars($text, $flags);
}
function value($values, $name) {
    if (array_key_exists($name, $values)) {
        echo escape($values[$name]);
    }
}
function error($errors, $name) {
    if (array_key_exists($name, $errors)) {
        echo '<p style="color:red">' . escape($errors[$name]) . '</p>';
    }
}
function peselValid($pesel) {
    if (!preg_match('#^[0-9]{11}$#', $pesel)) {
        return false;
    }
    $step = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];
    $sum1 = 0;
    for ($i = 0; $i < 10; $i++) {
        $sum1 += $step[$i] * $pesel[$i];
    }
    $sum2 = 10 - $sum1 % 10;
    return ($sum2 == 10 ? 0 : $sum2) == $pesel[10] ? true : false;
}

$success = false;
$errors = [];
$values = [];
$message = null;

if (isset($_POST['send'])) {
    $values['first_name'] = $_POST['first_name'];
    if (empty($values['first_name']) || mb_strlen($values['first_name']) > 50) {
        $errors['first_name'] = 'Musisz podać imię.';
    }
    $values['last_name'] = $_POST['last_name'];
    if (empty($values['last_name']) || mb_strlen($values['last_name']) > 50) {
        $errors['last_name'] = 'Musisz podać nazwisko.';
    }
    $values['grade_id'] = (integer) $_POST['grade_id'];
    if (!array_key_exists($values['grade_id'], $grades)) {
        $errors['grade_id'] = 'Musisz wybrać stopień instruktorski.';
    }
    $values['region_id'] = (integer) $_POST['region_id'];
    if (!array_key_exists($values['region_id'], $regions)) {
        $errors['region_id'] = 'Musisz wybrać chorągiew.';
    }
    $values['pesel'] = (float) preg_replace('#[^0-9]#', '', $_POST['pesel']);
    if (!peselValid($values['pesel'])) {
        $errors['pesel'] = 'Musisz podać poprawny numer PESEL.';
    }
    $values['address'] = $_POST['address'];
    if (empty($values['address']) || mb_strlen($values['address']) > 255) {
        $errors['address'] = 'Musisz podać adres zamieszkania.';
    }
    $values['service_id'] = (integer) $_POST['service_id'];
    if (!array_key_exists($values['service_id'], $services)) {
        $errors['service_id'] = 'Musisz wybrać rodzaj służby.';
    }
    $values['permissions'] = $_POST['permissions'];
    if (empty($values['permissions']) || mb_strlen($values['permissions']) > 255) {
        $errors['permissions'] = 'Musisz opisać swoje doświadczenie.';
    }
    $values['languages'] = $_POST['languages'];
    if (empty($values['languages']) || mb_strlen($values['languages']) > 255) {
        $errors['languages'] = 'Musisz opisać swoje umiejętności językowe.';
    }
    $values['profession'] = $_POST['profession'];
    if (empty($values['profession']) || mb_strlen($values['profession']) > 255) {
        $errors['profession'] = 'Musisz podać swój zawód.';
    }
    $values['phone'] = $_POST['phone'];
    if (!preg_match('#^[0-9 \+\-\(\)]{8,15}$#', $values['phone'])) {
        $errors['phone'] = 'Musisz podać poprawny numer telefonu';
    }
    $values['mail'] = $_POST['mail'];
    if (empty($values['mail']) || !filter_var($values['mail'], FILTER_VALIDATE_EMAIL) ||
        mb_strlen($values['mail']) > 40)
    {
        $errors['mail'] = 'Musisz podać poprawny adres e-mail.';
    }
    $values['service_time'] = $_POST['service_time'];
    if (empty($values['service_time']) || mb_strlen($values['service_time']) > 255) {
        $errors['service_time'] = 'Musisz podać czas służby.';
    }
    if (count($errors) == 0) {
        $statement1 =
            $db->prepare('SELECT COUNT(id) AS count FROM `sdm_volunteers` WHERE pesel = :pesel || mail = :mail');
        $statement1->bindValue(':pesel', $values['pesel']);
        $statement1->bindValue(':mail', $values['mail']);
        if ($statement1->execute()) {
            $data = $statement1->fetch(PDO::FETCH_ASSOC);
            if ($data['count'] > 0) {
                $message = 'Podany numer PESEL lub adres e-mail znajdują się już w naszej bazie.';
            } else {
                $statement2 = $db->query('SHOW TABLE STATUS LIKE "sdm_volunteers"');
                $data = $statement2->fetch(PDO::FETCH_ASSOC);
                $values['id'] = $data['Auto_increment'];
                $queryValues = [];
                foreach (array_keys($values) as $key) {
                    $queryValues[] = $key . ' = :' . $key;
                }
                $statement3 = $db->prepare('INSERT INTO `sdm_volunteers` SET ' . implode(', ', $queryValues));
                foreach ($values as $key => $value) {
                    $statement3->bindValue(':' . $key, $value);
                }
                if ($statement3->execute()) {
                    $success = true;
                    $message = 'Formularz został pomyślnie wysłany.';
                    mail('w.maj@krakowska.zhp.pl', 'Formularz ŚDM', 'Przesłano nowy formularz o ID ' . $values['id'] .
                        '.');
                } else {
                    $message = 'Wystąpiły błędy podczas wysyłania formularza - spróbuj ponownie.';
                }
            }
        } else {
            $message = 'Wystąpiły błędy podczas wysyłania formularza - spróbuj ponownie.';
        }
    }
}

?><!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Światowe Dni Młodzieży - formularz zgłoszeniowy wolontariuszy ZHP</title>
</head>
<body>
    <?php if ($success): ?>
        <h1><?php echo escape($message); ?></h1>
    <?php else: ?>
        <h1>Formularz zgłoszeniowy wolontariuszy ZHP na Światowe Dni Młodzieży</h1>
        <?php if (isset($message)): ?><h2><?php echo escape($message); ?></h2><?php endif; ?>
        <form action="/" method="POST">
            <dl>
                <dt>Imię:</dt>
                <dd>
                    <input type="text" name="first_name" value="<?php value($values, 'first_name'); ?>" required
                        maxlength="50">
                    <?php error($errors, 'first_name'); ?>
                </dd>
                <dt>Nazwisko:</dt>
                <dd>
                    <input type="text" name="last_name" value="<?php value($values, 'last_name'); ?>" required
                        maxlength="50">
                    <?php error($errors, 'last_name'); ?>
                </dd>
                <dt>Stopień instruktorski:</dt>
                <dd>
                    <select name="grade_id" required>
                        <option></option>
                        <?php foreach ($grades as $id => $grade): ?>
                            <option value="<?php echo $id; ?>"<?php echo array_key_exists('grade_id', $values) &&
                                $values['grade_id'] == $id ? ' selected' : ''; ?>>
                                <?php echo escape($grade); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php error($errors, 'grade_id'); ?>
                </dd>
                <dt>Chorągiew (przydział służbowy):</dt>
                <dd>
                    <select name="region_id" required>
                        <option></option>
                        <?php foreach ($regions as $id => $region): ?>
                            <option value="<?php echo $id; ?>"<?php echo array_key_exists('region_id', $values) &&
                                $values['region_id'] == $id ? ' selected' : ''; ?>>
                                <?php echo escape($region); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php error($errors, 'region_id'); ?>
                </dd>
                <dt>PESEL:</dt>
                <dd>
                    <input type="text" name="pesel" value="<?php value($values, 'pesel'); ?>" required
                        pattern="[0-9]{11}" maxlength="11">
                    <?php error($errors, 'pesel'); ?>
                </dd>
                <dt>Adres zamieszkania:</dt>
                <dd>
                    <input type="text" name="address" value="<?php value($values, 'address'); ?>" required
                        maxlength="255">
                    <?php error($errors, 'address'); ?>
                </dd>
                <dt>Rodzaj służby:</dt>
                <dd>
                    <select name="service_id" required>
                        <option></option>
                        <?php foreach ($services as $id => $service): ?>
                            <option value="<?php echo $id; ?>"<?php echo array_key_exists('service_id', $values) &&
                                $values['service_id'] == $id ? ' selected' : ''; ?>>
                                <?php echo escape($service['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php error($errors, 'service_id'); ?>
                </dd>
                <dt>Posiadane uprawnienia, przebyte szkolenia, doświadczenie:</dt>
                <dd>
                    <input type="text" name="permissions" value="<?php value($values, 'permissions'); ?>" required
                        maxlength="255">
                    <?php error($errors, 'permissions'); ?>
                </dd>
                <dt>Języki obce i stopień zaawansowania:</dt>
                <dd>
                    <input type="text" name="languages" value="<?php value($values, 'languages'); ?>" required
                        maxlength="255">
                    <?php error($errors, 'languages'); ?>
                </dd>
                <dt>Zawód:</dt>
                <dd>
                    <input type="text" name="profession" value="<?php value($values, 'profession'); ?>" required
                        maxlength="255">
                    <?php error($errors, 'profession'); ?>
                </dd>
                <dt>Numer telefonu:</dt>
                <dd>
                    <input type="text" name="phone" value="<?php value($values, 'phone'); ?>" required maxlength="15">
                    <?php error($errors, 'phone'); ?>
                </dd>
                <dt>E-mail:</dt>
                <dd>
                    <input type="text" name="mail" value="<?php value($values, 'mail'); ?>" required maxlength="40">
                    <?php error($errors, 'mail'); ?>
                </dd>
                <dt>Czas służby:</dt>
                <dd>
                    <input type="text" name="service_time" value="<?php value($values, 'service_time'); ?>" required
                       maxlength="255">
                    <?php error($errors, 'service_time'); ?>
                </dd>
                <dd>
                    <input type="submit" name="send" value="Wyślij">
                </dd>
            </dl>
        </form>
    <?php endif; ?>
</body>
</html>
