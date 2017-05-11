
Ext.define('Shopware.apps.dotmailerEmailMarketing.model.Main', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'dotmailerEmailMarketing',
            detail: 'Shopware.apps.dotmailerEmailMarketing.view.detail.Container'
        };
    },


    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'name', type: 'string', useNull: false }
    ]
});

