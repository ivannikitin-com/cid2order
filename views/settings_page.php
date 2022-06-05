<?php
/**
 * Страница настроек плагина
 * Выполняется в контексте метода Cid2order_settings::show_settings_page()
 */
?>
<div class="wrap">
    <h2><?php echo get_admin_page_title() ?></h2>

    <p><?php _e( 'cid2order - плагин для Woocommerce, который сохраняет ClientID Google Analytics, Яндекс.Метрика и других счетчиков в полях заказа. Также плагин может сохранять ClientIds в формах ContactForm 7.', CID2ORDER )?></p>
    <p><?php _e( 'Выберите нужные интеграции плагина с помощью галочек. Другие параметры лучше не трогать.', CID2ORDER )?></p>

    <form action="options.php" method="POST">
        <?php
            settings_fields( self::OPTION_GROUP );      // скрытые защитные поля
            do_settings_sections( CID2ORDER );          // Вывод на странице.
            submit_button();
        ?>
    </form>
</div>