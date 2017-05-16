
Ext.define('Shopware.apps.DotmailerEmailMarketing.view.list.List', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.dotmailer-email-marketing-listing-grid',
    region: 'center',

    configure: function() {
        return {
            detailWindow: 'Shopware.apps.DotmailerEmailMarketing.view.detail.Window'
        };
    }
});
