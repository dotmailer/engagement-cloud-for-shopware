
Ext.define('Shopware.apps.dotmailerEmailMarketing.store.Main', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'dotmailerEmailMarketing'
        };
    },
    model: 'Shopware.apps.dotmailerEmailMarketing.model.Main'
});