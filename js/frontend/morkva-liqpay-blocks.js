const settings_mrkv_liqpay = window.wc.wcSettings.getSetting( 'morkva-liqpay_data', {} );
const label_mrkv_liqpay = window.wp.htmlEntities.decodeEntities( settings_mrkv_liqpay.title );

const htmlToElem_mrkv_liqpay = ( html ) => wp.element.RawHTML( { children: html } );

const Mrkv_Liqpay_Gateway = {
    name: 'morkva-liqpay',
    label: label_mrkv_liqpay,
    content: htmlToElem_mrkv_liqpay(settings_mrkv_liqpay.description),
    edit: htmlToElem_mrkv_liqpay(settings_mrkv_liqpay.description),
    canMakePayment: () => true,
    ariaLabel: label_mrkv_liqpay,
    supports: {
        features: settings_mrkv_liqpay.supports,
    },
};
window.wc.wcBlocksRegistry.registerPaymentMethod( Mrkv_Liqpay_Gateway );