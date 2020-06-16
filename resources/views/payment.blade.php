<?php
    use Zarinpal\Laravel\Facade\Zarinpal;
    $results = Zarinpal::request(
        env('APP_URL') . '/api/plan/pay/done',
        500,
        "عنواان"
    );

    return Zarinpal::redirect();
?>
