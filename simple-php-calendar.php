<?php declare(strict_types=1);

$month = date('Y-m');
$holidays = [
    '2024-02-20' => 'Chin National Day',
];

$calendar = calendar($month, $holidays);

$title = $calendar['title'];
$header = $calendar['day_name'];
$weeks = $calendar['weeks'];

/**
 * Generate calendar data
 * 
 * @param string     $month    Month string (yyyy-mm)
 * @param array|null $holidays Holiday in array (eg [ '2024-12-25' => 'Christmas' ])
 * 
 * @return array
 */
function calendar(string $month, array $holidays = null): array
{
    // build template for week days
    $weekDayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    $weekTemplate = [];
    foreach ($weekDayNames as $name) {
        $weekTemplate[$name] = [
            'day' => null,
            'class' => null,
            'text' => null,
        ];
    }

    // last day of month
    $monthLast = date('t', strtotime($month));

    // number of month start date
    $dayNumber = date("N", strtotime($month));

    // start from sunday
    $start = 1 - $dayNumber;

    $days = [];
    $dayKey = -1; //for array key 0
    for ($i = $start; $i <= $monthLast; $i++) {
        $dayKey = $dayKey + 1;
        // reset day key
        if ($dayKey === 7) {
            $dayKey = 0;
        }
        $days[] = [
            $weekDayNames[$dayKey] => $i > 0 ? $i : null
        ];
    }

    // build weekly date data
    $weekTitle = ['first', 'second', 'third', 'fourth', 'fifth', 'sixth'];
    $chunked = array_chunk($days, 7, true);
    $weeks = [];
    $titleKey = -1; //for array key 0
    foreach ($chunked as $weekDays) {
        $titleKey = $titleKey + 1;
        $days = [];
        foreach ($weekDays as $day) {
            $classAdd = '';
            $arrKey  = array_keys($day)[0];

            // for today
            if (($month === date('Y-m')) && (intval($day[$arrKey]) === intval(date('d')))) {
                $classAdd .= ' today';
            }

            // for holidays
            $dayText = null;
            if (is_array($holidays)) {
                if ($day[$arrKey] != null) {
                    $dateNumber = str_pad(strval($day[$arrKey]), 2, '0', STR_PAD_LEFT);
                    $dateString = $month . '-' . $dateNumber;
                    if (array_key_exists($dateString, $holidays)) {
                        $classAdd .= ' holiday';
                        $dayText = $holidays[$dateString];
                    }
                }
            }

            $days[$arrKey] = [
                'day' => $day[$arrKey],
                'class' => $arrKey . $classAdd,
                'text' => $dayText,
            ];
        }

        // for blank days
        $weeks[$weekTitle[$titleKey]] = array_merge($weekTemplate, $days);
    }

    return [
        'title' => date('F Y', strtotime($month)),
        'day_name' => $weekDayNames,
        'weeks' => $weeks,
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Simple Calendar</title>
    <style>
        .calendar-wrapper {
            padding: 0.5rem;
        }

        .calendar-day-row {
            display: flex;
            justify-content: center;
        }

        .calendar-title {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            line-height: 2rem;
            padding-bottom: 1.25rem;
        }

        .calendar-day-name {
            width: 14%;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 1.75rem;
            border: 1px solid #000;
            text-align: center;
            padding: 1rem;
        }

        .calendar-day-name.sunday {
            border-top-left-radius: 0.5rem;
            background-color: red;
        }

        .calendar-day-name.saturday {
            border-top-right-radius: 0.5rem;
            background-color: red;
        }

        .calendar-day {
            width: 14%;
            min-height: 12vh;
            font-size: 2rem;
            border: 1px solid #000;
            text-align: right;
            padding: 1rem;
        }

        .calendar-day.sunday,
        .calendar-day.saturday,
        .calendar-day.holiday {
            color: red;
        }

        .calendar-day.today {
            font-weight: bold;
            border-color: blue;
        }

        .calendar-day .day-text {
            font-size: 0.9rem;
            text-align: center;
            font-weight: 300;
            color: grey;
        }
</style>
</head>
<body>
<div class="calendar-wrapper">
    <div class="calendar-title">
        <?php echo $title ?>
    </div>
    <div class="calendar-day-row">
        <?php foreach ($header as $name): ?>
        <div class="calendar-day-name <?php echo $name ?>"><?php echo ucfirst($name) ?></div>
        <?php endforeach ?>
    </div>
    <?php foreach ($weeks as $row): ?>
    <div class="calendar-day-row">
        <?php foreach ($row as $day): ?>
        <div class="calendar-day <?php echo $day['class'] ?>">
            <?php echo $day['day'] ?>
            <?php if (isset($day['text'])) : ?>
            <div class="day-text">
                <?php echo $day['text'] ?>
            </div>
            <?php endif ?>
        </div>
        <?php endforeach ?>
    </div>
    <?php endforeach ?>
</div>
</body>
</html>
