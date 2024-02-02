// Libreria para reordenar los Tab
Ext.Loader.setPath('Ext.ux', './js/extjs4/includes/ux/');

Ext.define('SEMTI.view.App', {
	
	extend: 'Ext.Viewport',
	layout: 'border',
	id: 'mainviewport',
	
	requires: ['SEMTI.view.portada.Portada','Ext.tab.*','Ext.ux.TabReorderer'],
	
	initComponent: function() {
		
		var polo = (localStorage.getItem('polo_name') != 'Todos') ? localStorage.getItem('polo_name') : "";
		Ext.apply(this, {
		
			items: [
			// create instance immediately
			{
				region: 'north',
				xtype: 'headerapp'
			},
			{
				region: 'south',
				//xtype: 'mensajes',
				//split: false,
				height: 37,
				id: 'syst-footer',
				//collapsible: false,
				//collapsed: true,
				html: '&copy A.E.I. UCM-BBI '+ polo +' - Servicios Inform\xE1ticos '+new Date().getFullYear()+'<span class="system_version"><span class="desc">Sistema de Gesti\xF3n de</span> Garant\xEDa | Versi\xF3n: 2.0 Beta</span>',
				//titleAlign: 'right',
				/*header: {
					xtype: 'header',
					titlePosition: 0,
					defaults: {
						padding: '0 5 0 0'
					},
					cls: 'header-text',
					items: [
						{
							xtype: 'label'
							//text: ''
						}
					]
				},*/
				//margins: '0 0 0 0'
			}, 
			{
				region: 'west',
				stateId: 'navigation-panel',
				id: 'west-panel',
				title: 'MAPA DE NAVEGACI\xD3N',
				split: true,
				width: 300,
				minWidth: 300,
				maxWidth: 350,
				collapsible: true,
				animCollapse: true,
				xtype: 'mainaccordion'
				/*margins: '0 0 0 5',
				layout: 'accordion',
				items: [{
					html: '<div id="west" class="x-hide-display"></div>',
					title: 'Subsistemas',
					iconCls: 'sistemas',
					xtype:'treesistemas'
				}, {
					title: 'Acerca de SEMTI',
					html: '<center><div id="south"><img src="resources/images/semti.png" align="center"></div><div id="gestion_title">INNOVACI&Oacute;N AL SERVICIO DE SUS NECESIDADES</div><div id="gestion_content"><p>Eleve los niveles de productividad y eficiencia obrera de su empresa, de la mano de profesionales. <p> <i>Vemos en el exito de nuestros clientes, la garant\xEDa de nuestro futuro.</i></p></div></center>',
					iconCls: 'info',
					scroll: true,
					autoScroll: true
				}]*/
			},
			Ext.create('Ext.tab.Panel', {
				region: 'center', // una region central siempre es requerida para border layout
				deferredRender: false,
				plugins: Ext.create('Ext.ux.TabReorderer'),
				id: 'PTpanel',
				activeTab: 0,
				// Primer Tab
				items: [{
					xtype: 'portadapanel'
				}]
			})
			]
		
		})
	
	this.callParent(arguments);
    }
});