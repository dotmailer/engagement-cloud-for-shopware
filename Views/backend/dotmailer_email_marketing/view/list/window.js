
Ext.define('Shopware.apps.DotmailerEmailMarketing.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.dotmailer-email-marketing-list-window',
    height: 450,
    title : '{s name=window_title}DotmailerEmailMarketing listing{/s}',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.DotmailerEmailMarketing.view.list.List',
            listingStore: 'Shopware.apps.DotmailerEmailMarketing.store.Main'
        };
    }
});