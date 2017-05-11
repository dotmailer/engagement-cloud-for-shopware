
Ext.define('Shopware.apps.dotmailerEmailMarketing.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.dotmailer-email-marketing-list-window',
    height: 450,
    title : '{s name=window_title}dotmailerEmailMarketing listing{/s}',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.dotmailerEmailMarketing.view.list.List',
            listingStore: 'Shopware.apps.dotmailerEmailMarketing.store.Main'
        };
    }
});