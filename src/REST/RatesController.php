<?php
namespace ElDoviz\REST;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_REST_Controller;
use WP_REST_Server;
use WP_Error;

/**
 * REST API controller for El Doviz data.
 *
 * Provides read‑only endpoints for exchange rates.
 */
class RatesController extends WP_REST_Controller {
    /**
     * Namespace for the REST routes.
     *
     * @var string
     */
    protected $namespace = 'ledoviz-turkish-exchange-rates/v1';

    /**
     * Register routes.
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/rates', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_rates' ],
                'permission_callback' => [ $this, 'public_permission' ],
                'args'                => [
                    'source' => [
                        'description' => esc_html__( 'Data source (tcmb).', 'el-doviz' ),
                        'type'        => 'string',
                        'enum'        => [ 'tcmb' ],
                        'default'     => 'tcmb',
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
            ],
        ] );
    }

    /**
     * Permission callback – public read‑only endpoint.
     */
    public function public_permission( $request ) {
        return true;
    }

    /**
     * Retrieve data from the requested source.
     */
    public function get_rates( $request ) {
        $source = $request->get_param( 'source' );
        $fetcher = new \ElDoviz\Service\DataFetcher( new \ElDoviz\Service\CacheManager() );
        $data    = $fetcher->fetch( $source, HOUR_IN_SECONDS );
        if ( is_wp_error( $data ) ) {
            return new WP_Error( 'ledoviz_turkish_exchange_rates_rest_error', $data->get_error_message(), [ 'status' => 500 ] );
        }
        return rest_ensure_response( $data );
    }
}
?>
