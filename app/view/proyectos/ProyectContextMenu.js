Ext.define('SEMTI.view.proyectos.ProyectContextMenu', {
    extend: 'Ext.menu.Menu',
    xtype: 'proyectContextMenu',
    items: [
        {
            text: 'Nueva Zona',
            iconCls: 'icon-add',
            id: 'proyects-new-zone'
        }, {
            text: 'Nuevo Objeto',
            iconCls: 'icon-add',
            id: 'proyects-new-object'
        }, {
            text: 'Nuevo Local',
            iconCls: 'icon-add',
            id: 'proyects-new-parte'
        }, {
            text: 'Modificar Proyecto',
            iconCls: 'icon-edit',
            id: 'proyects-rename-proyect'
        }, {
            text: 'Modificar Zona',
            iconCls: 'icon-edit',
            id: 'proyects-rename-zone'
        }, {
            text: 'Renombrar Objecto',
            iconCls: 'icon-edit',
            id: 'proyects-rename-object'
        }, {
            text: 'Ver SD',
            iconCls: 'icon_SD',
            id: 'show-sd'
        },
        '-',
        {
            text: 'Eliminar Proyecto',
            iconCls: 'icon-delete',
            id: 'proyects-delete-proyect'
        },
        {
            text: 'Eliminar Zona',
            iconCls: 'icon-delete',
            id: 'proyects-delete-zone'
        },
        {
            text: 'Eliminar Objeto',
            iconCls: 'icon-delete',
            id: 'proyects-delete-object'
        }
    ],
    /**
     * Associates this menu with a specific list.
     * @param {SimpleTasks.model.List} list
     */
    setList: function(list) {
        this.list = list;
    },
    /**
     * Gets the list associated with this menu
     * @return {Task.model.List}
     */
    getList: function() {
        return this.list;
    }

});