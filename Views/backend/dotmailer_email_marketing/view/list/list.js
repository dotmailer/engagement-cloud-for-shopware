
Ext.define('Shopware.apps.dotmailerEmailMarketing.view.list.List', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.dotmailer-email-marketing-listing-grid',
    region: 'center',

    configure: function() {
        return {
            detailWindow: 'Shopware.apps.dotmailerEmailMarketing.view.detail.Window'
        };
    }
});
