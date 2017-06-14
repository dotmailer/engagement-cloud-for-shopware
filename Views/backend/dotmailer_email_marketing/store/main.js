
Ext.define('Shopware.apps.DotmailerEmailMarketing.store.Main', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'DotmailerEmailMarketing'
        };
    },
    model: 'Shopware.apps.DotmailerEmailMarketing.model.Main'
});