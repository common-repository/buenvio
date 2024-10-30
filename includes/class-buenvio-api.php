<?php

final class Buenvio_API {
    final static function postRequest(string $endpoint, array $params) {
        $buenvio_data = get_option('buenvio');
        if(!isset($buenvio_data)) {
            return null;
        }

        return json_decode(
            wp_remote_retrieve_body(
                wp_remote_post(
                    'https://api.buenvio.com/shopify/v1'.$endpoint,
                    [
                        'headers' => [
                            'Authorization' => $buenvio_data['token'],
                        ],
                        'body' => $params + ['sandbox' => true],
                    ]
                )
            )
        );
    }

    final static function deleteRequest(string $endpoint, array $params) {
        $buenvio_data = get_option('buenvio');
        if(!isset($buenvio_data)) {
            return null;
        }

        return json_decode(
            wp_remote_retrieve_body(
                wp_remote_request(
                    'https://api.buenvio.com/shopify/v1'.$endpoint,
                    [
                        'headers' => [
                            'Authorization' => $buenvio_data['token'],
                        ],
                        'body' => $params + ['sandbox' => true],
                        'method' => 'DELETE',
                    ]
                )
            )
        );
    }

    final static function getRequest(string $endpoint, $params) {
        $buenvio_data = get_option('buenvio');
        if(!isset($buenvio_data)) {
            return null;
        }

        return json_decode(
            wp_remote_retrieve_body(
                wp_remote_get(
                    $endpoint,
                    [
                        'body' => $params + ['sandbox' => true],
                    ]
                )
            )
        );
    }
}