<?php
/**
 * cid2order
 *
 * @package           cid2order
 * @author            Ivan Nikitin
 * @copyright         2021 IvanNikitin.com
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       cid2order
 * Plugin URI:        https://github.com/ivannikitin-com/cid2order
 * Description:       Плагин для Woocommerce, который сохраняет ClientID Google Analytics, Яндекс.Метрика и других счетчиков в полях заказа и в формах ContactForm 7.
 * Version:           0.2
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            Иван Никитин
 * Author URI:        https://ivannikitin.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/ivannikitin-com/cid2order
 * Text Domain:       cid2order
 * Domain Path:       /lang
 */
// Напрямую не вызываем!
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* Глобальные константы плагина */
define( 'CID2ORDER', 'cid2order' );	                          // Text Domain
define( 'CID2ORDER_DIR', basename( dirname( __FILE__ ) ) );	  // Папка плагина

/* Файлы ядра плагина */
require_once( 'classes/plugin.php' );
require_once( 'classes/settings.php' );

/**
 * Запуск плагина
 */
new Cid2order_plugin();