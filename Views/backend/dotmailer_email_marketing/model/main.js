
Ext.define('Shopware.apps.DotmailerEmailMarketing.model.Main', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'DotmailerEmailMarketing',
            detail: 'Shopware.apps.DotmailerEmailMarketing.view.detail.Container'
        };
    },


    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'name', type: 'string', useNull: false }
    ]
});

