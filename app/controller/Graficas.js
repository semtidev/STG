Ext.define('SEMTI.controller.Graficas', {
    extend: 'Ext.app.Controller',
    stores: ['Gtiasdpendientes'],
    models: [],
    views: [
        'graficas.GtiaSeguimSDPendientes',
        'graficas.GtiaGridSDPendientes',
        'graficas.GtiaSeguimSDConSumAEH',
        'graficas.GtiaSeguimDemoraProm',
        'graficas.GtiaSeguimSDNoProceden',
        'graficas.GtiaSeguimComparativa',
        'graficas.GtiaSeguimDemoraPromAEH',
        'graficas.GtiaSeguimDemoraPromConNoAEHNoSum',
        'graficas.GtiaSeguimDemoraPromConAEHNoSum',
        'graficas.GtiaSeguimDemoraPromConAEHSum',
        'graficas.GtiaSeguimDemoraPromConNoAEHSum',
        'graficas.GtiaSeguimTipoDefectoReportesSD'
    ],
    refs: [{
            ref: 'mainPanelChartSDPendientes',
            selector: 'GtiaSeguimSDPendientes'
        }, {
            ref: 'mainPanelChartSDConAehSum',
            selector: 'GtiaSeguimSDConSumAEH'
        }, {
            ref: 'mainPanelChartSDDemora',
            selector: 'GtiaSeguimDemoraProm'
        }, {
            ref: 'mainPanelChartSDDemoraAEH',
            selector: 'GtiaSeguimDemoraPromAEH'
        }, {
            ref: 'mainPanelChartSDDemoraNoAEHNoSum',
            selector: 'GtiaSeguimDemoraPromConNoAEHNoSum'
        }, {
            ref: 'mainPanelChartSDDemoraAEHNoSum',
            selector: 'GtiaSeguimDemoraPromConAEHNoSum'
        }, {
            ref: 'mainPanelChartSDDemoraConAEHSum',
            selector: 'GtiaSeguimDemoraPromConAEHSum'
        }, {
            ref: 'mainPanelChartSDDemoraConNoAEHSum',
            selector: 'GtiaSeguimDemoraPromConNoAEHSum'
        }, {
            ref: 'mainPanelChartSDDefectos',
            selector: 'GtiaSeguimTipoDefectoReportesSD'
        }, {
            ref: 'mainPanelChartSDNoProceden',
            selector: 'GtiaSeguimSDNoProceden'
        }, {
            ref: 'mainPanelChartSDComparativa',
            selector: 'GtiaSeguimComparativa'
    }],
    
    init: function() {

        this.control({
            '#viewSDPendientesProyects': {
                change: this.filterSDPendProyect
            },
            '#viewSDPendientesTipo': {
                change: this.filterSDPendTipo
            },
            'GtiaSeguimSDPendientes button[action=actualizar]': {
                click: this.updateSeguimSDPendientes
            },
            'GtiaSeguimSDPendientes button[action=informe]': {
                click: this.reportSeguimSDPendientes
            },
            //////////////////////////////////////////////////
            '#viewSDConAehSumProyects': {
                change: this.filterSDConAehSumProyect
            },
            '#viewSDConAehSumEstado': {
                change: this.filterSDConAehSumEstado
            },
            '#viewSDConAehSumTipo': {
                change: this.filterSDConAehSumTipo
            },
            '#ChartSDConSumAehStart': {
                change: this.filterSDConAehSumRango
            },
            '#ChartSDConSumAehEnd': {
                change: this.filterSDConAehSumRango
            },
            ///////////////////////////////////////////////////
            '#viewSDDemoraProyects': {
                change: this.filterSDDemoraProyect
            },
            '#viewSDDemoraEstado': {
                change: this.filterSDDemoraEstado
            },
            '#viewSDDemoraTipo': {
                change: this.filterSDDemoraTipo
            },
            '#ChartDemSDStart': {
                change: this.filterSDDemoraRango
            },
            '#ChartDemSDEnd': {
                change: this.filterSDDemoraRango
            },
            'GtiaSeguimDemoraProm #ChartSDDemoraField': {
                specialkey: this.ChartSDDemoraMetaKey
            },
            ////////////////////////////////////////////////////
            '#viewSDDemoraAEHProyects': {
                change: this.filterSDDemoraAEHProyect
            },
            '#viewSDDemoraAEHEstado': {
                change: this.filterSDDemoraAEHEstado
            },
            '#viewSDDemoraAEHTipo': {
                change: this.filterSDDemoraAEHTipo
            },
            '#ChartDemSDAehStart': {
                change: this.filterSDDemoraAEHRango
            },
            '#ChartDemSDAehEnd': {
                change: this.filterSDDemoraAEHRango
            },
            /////////////////////////////////////////////////////
            '#viewSDDemoraNoAEHNoSumProyects': {
                change: this.filterSDDemoraNoAEHNoSumProyect
            },
            '#viewSDDemoraNoAEHNoSumEstado': {
                change: this.filterSDDemoraNoAEHNoSumEstado
            },
            '#viewSDDemoraNoAEHNoSumTipo': {
                change: this.filterSDDemoraNoAEHNoSumTipo
            },
            '#ChartDemConNoAehNoSumStart': {
                change: this.filterSDDemoraNoAEHNoSumRango
            },
            '#ChartDemConNoAehNoSumEnd': {
                change: this.filterSDDemoraNoAEHNoSumRango
            },
            //////////////////////////////////////////////////////
            '#viewSDDemoraAEHNoSumProyects': {
                change: this.filterSDDemoraAEHNoSumProyect
            },
            '#viewSDDemoraAEHNoSumEstado': {
                change: this.filterSDDemoraAEHNoSumEstado
            },
            '#viewSDDemoraAEHNoSumTipo': {
                change: this.filterSDDemoraAEHNoSumTipo
            },
            '#ChartDemConAehNoSumStart': {
                change: this.filterSDDemoraAEHNoSumRango
            },
            '#ChartDemConAehNoSumEnd': {
                change: this.filterSDDemoraAEHNoSumRango
            },
            //////////////////////////////////////////////////////
            '#viewSDDemoraConAEHSumProyects': {
                change: this.filterSDDemoraConAEHSumProyect
            },
            '#viewSDDemoraConAEHSumEstado': {
                change: this.filterSDDemoraConAEHSumEstado
            },
            '#viewSDDemoraConAEHSumTipo': {
                change: this.filterSDDemoraConAEHSumTipo
            },
            '#ChartDemConAehSumStart': {
                change: this.filterSDDemoraConAEHSumRango
            },
            '#ChartDemConAehSumEnd': {
                change: this.filterSDDemoraConAEHSumRango
            },
            /////////////////////////////////////////////////////
            '#viewSDDemoraConNoAEHSumProyects': {
                change: this.filterSDDemoraConNoAEHSumProyect
            },
            '#viewSDDemoraConNoAEHSumEstado': {
                change: this.filterSDDemoraConNoAEHSumEstado
            },
            '#viewSDDemoraConNoAEHSumTipo': {
                change: this.filterSDDemoraConNoAEHSumTipo
            },
            '#ChartDemConNoAEHSumStart': {
                change: this.filterSDDemoraConNoAEHSumRango
            },
            '#ChartDemConNoAEHSumEnd': {
                change: this.filterSDDemoraConNoAEHSumRango
            },
            ////////////////////////////////////////////////////
            '#viewSDDefectosProyects': {
                change: this.filterSDDefectosProyect
            },
            '#viewSDDefectosEstado': {
                change: this.filterSDDefectosEstado
            },
            '#viewSDDefectosTipo': {
                change: this.filterSDDefectosTipo
            },
            '#ChartDefectosStart': {
                change: this.filterSDDefectosRango
            },
            '#ChartDefectosEnd': {
                change: this.filterSDDefectosRango
            },
            //////////////////////////////////////////////////////
            '#viewSDNoProcedenProyects': {
                change: this.filterSDNoProcedenProyect
            },
            '#viewSDNoProcedenTipo': {
                change: this.filterSDNoProcedenTipo
            },
            '#ChartSDNoProcedenStart': {
                change: this.filterSDNoProcedenRango
            },
            '#ChartSDNoProcedenEnd': {
                change: this.filterSDNoProcedenRango
            },
            //////////////////////////////////////////////////////
            '#viewSDComparativaProyects': {
                change: this.filterSDComparativaProyect
            },
            '#viewSDComparativaEstado': {
                change: this.filterSDComparativaEstado
            },
            '#viewSDComparativaTipo': {
                change: this.filterSDComparativaTipo
            }
        });
    },
    
    /***********************************************
     ********       REPORTES, GRAFICAS       *******
     ***********************************************/

    updateSeguimSDPendientes: function() {

        var mainPanel = this.getMainPanelChartSDPendientes();
        mainPanel.down('GtiaGridSDPendientes').getStore().load();
        mainPanel.down('GtiaChartPanelSDPendientes').down('GtiaChartSDPendientes').getStore().load();
    },
    
    reportSeguimSDPendientes: function() {

        let ipserver = localStorage.getItem('ipserver');
        window.open('http://'+ipserver+'/semti.garantia/php/garantia/GtiaExportSeguimSDPendientes.php', '_blank');
    },
    
    filterSDPendProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo = '';
        
        var tipo = Ext.getCmp('viewSDPendientesTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Comportamiento del Estado de las SD ';
        }
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Comportamiento del Estado de las SD ';
        }
        if(tipo == 'SD Comunes'){
            titulo = 'Comportamiento del Estado de las SD Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Comportamiento del Estado de las SD Habitacionales ';
        }
        if(newValue.length > 0 && newValue != 'Todos'){
            titulo = titulo + newValue;
        }

        var filtro = newValue + '.' + tipo,
            store  = this.getGtiasdpendientesStore();    
            
        store.getProxy().setExtraParam("listar", filtro);
        store.load({
            callback: function() {
                var frame = Ext.getCmp('iframeSDPendChart');                 
                if(frame && frame.rendered ){
                    let ipserver = localStorage.getItem('ipserver');
                    frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_estados.php?listar='+filtro+'&titulo='+ titulo;
                }                    
            }                               
        });        
    },
            
    filterSDPendTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo = '';
        
        var proyecto = Ext.getCmp('viewSDPendientesProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo   = 'Comportamiento del Estado de las SD ';
        }
        if (newValue === null) {
            newValue = 'Todas';
            titulo   = 'Comportamiento del Estado de las SD ';
        }
        if(newValue == 'SD Comunes'){
            titulo = 'Comportamiento del Estado de las SD Comunes ';
        }
        if(newValue == 'SD Habitaciones'){
            titulo = 'Comportamiento del Estado de las SD Habitacionales ';
        }
        if(proyecto.length > 0 && proyecto != 'Todos'){
            titulo = titulo + proyecto;
        }

        var filtro = proyecto + '.' + newValue,
            store  = this.getGtiasdpendientesStore();    
            
        store.getProxy().setExtraParam("listar", filtro);
        store.load({
            callback: function() {
                var frame = Ext.getCmp('iframeSDPendChart');                 
                if(frame && frame.rendered ){
                    let ipserver = localStorage.getItem('ipserver');
                    frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_estados.php?listar='+filtro+'&titulo='+ titulo;
                }                    
            }                               
        });        
    },
    
    /////////////////////////////////////////////////////////////////////////
    
    filterSDConAehSumProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDConAehSum();
            
        viewPanel.setLoading('Cargando...');
                    
        var tipo = Ext.getCmp('viewSDConAehSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        var estado = Ext.getCmp('viewSDConAehSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        var desde = Ext.getCmp('ChartSDConSumAehStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartSDConSumAehEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Comportamiento de SD Comunes Const/Sumin/AEH ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Comportamiento de SD Habitacionales Const/Sumin/AEH ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + newValue;
        }

        var filtro = newValue + '.' + estado + '.' + tipo,   
            frame = Ext.getCmp('iframeSDConAehSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_con_sum_aeh.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000);          
        }            
    },
    
    filterSDConAehSumEstado: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDConAehSum();
            
        viewPanel.setLoading('Cargando...');
        
        var tipo = Ext.getCmp('viewSDConAehSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        var proyecto = Ext.getCmp('viewSDConAehSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        var desde = Ext.getCmp('ChartSDConSumAehStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartSDConSumAehEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Comportamiento de SD Comunes Const/Sumin/AEH ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Comportamiento de SD Habitacionales Const/Sumin/AEH ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + newValue + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + newValue + '.' + tipo,   
            frame = Ext.getCmp('iframeSDConAehSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_con_sum_aeh.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                    
    },
    
    filterSDConAehSumTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDConAehSum();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDConAehSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        var proyecto = Ext.getCmp('viewSDConAehSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        var desde = Ext.getCmp('ChartSDConSumAehStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartSDConSumAehEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        if(newValue == 'SD Comunes'){
            titulo = 'Comportamiento de SD Comunes Const/Sumin/AEH ';
        }
        if(newValue == 'SD Habitaciones'){
            titulo = 'Comportamiento de SD Habitacionales Const/Sumin/AEH ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + newValue,   
            frame = Ext.getCmp('iframeSDConAehSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_con_sum_aeh.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                      
    },
    
    filterSDConAehSumRango: function() {
        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDConAehSum();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDConAehSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        var proyecto = Ext.getCmp('viewSDConAehSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        var tipo = Ext.getCmp('viewSDConAehSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Comportamiento de SD Const/Sumin/AEH ';
        }
        
        var desde = Ext.getCmp('ChartSDConSumAehStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartSDConSumAehEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
                
        if(tipo == 'SD Comunes'){
            titulo = 'Comportamiento de SD Comunes Const/Sumin/AEH ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Comportamiento de SD Habitacionales Const/Sumin/AEH ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + tipo,   
            frame = Ext.getCmp('iframeSDConAehSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_con_sum_aeh.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                   
    },
    
    //////////////////////////////////////////////////////////////////////////
    
    filterSDDemoraProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemora();
            
        viewPanel.setLoading('Cargando...');
                    
        var tipo = Ext.getCmp('viewSDDemoraTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        var estado = Ext.getCmp('viewSDDemoraEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        var desde = Ext.getCmp('ChartDemSDStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemSDEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en la Solución de los Reportes Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en la Solución de los Reportes Habitacionales ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + newValue;
        }

        var filtro = newValue + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000);          
        }            
    },
    
    filterSDDemoraEstado: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemora();
            
        viewPanel.setLoading('Cargando...');
        
        var tipo = Ext.getCmp('viewSDDemoraTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        var desde = Ext.getCmp('ChartDemSDStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemSDEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en la Solución de los Reportes Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en la Solución de los Reportes Habitacionales ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + newValue + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + newValue + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                    
    },
    
    filterSDDemoraTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemora();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        var desde = Ext.getCmp('ChartDemSDStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemSDEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        if(newValue == 'SD Comunes'){
            titulo = 'Demora Promedio en la Solución de los Reportes Comunes ';
        }
        if(newValue == 'SD Habitaciones'){
            titulo = 'Demora Promedio en la Solución de los Reportes Habitacionales ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + newValue,   
            frame  = Ext.getCmp('iframeSDDemoraChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                      
    },
    
    filterSDDemoraRango: function() {
        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemora();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        var tipo = Ext.getCmp('viewSDDemoraTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        var desde = Ext.getCmp('ChartDemSDStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemSDEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
                
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en la Solución de los Reportes Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en la Solución de los Reportes Habitacionales ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                
    },
    
    ///////////////////////////////////////////////////////////////////////
    
    filterSDDemoraAEHProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraAEH();
            
        viewPanel.setLoading('Cargando...');
                    
        var tipo = Ext.getCmp('viewSDDemoraAEHTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        var estado = Ext.getCmp('viewSDDemoraAEHEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        var desde = Ext.getCmp('ChartDemSDAehStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemSDAehEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en la Solución de los Reportes AEH Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en la Solución de los Reportes AEH Habitacionales ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + newValue;
        }

        var filtro = newValue + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraAEHChart');
            //meta   = Ext.getCmp('ChartSDDemoraAEHField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_aeh.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000);          
        }           
    },
    
    filterSDDemoraAEHEstado: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraAEH();
            
        viewPanel.setLoading('Cargando...');
        
        var tipo = Ext.getCmp('viewSDDemoraAEHTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraAEHProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        var desde = Ext.getCmp('ChartDemSDAehStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemSDAehEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en la Solución de los Reportes AEH Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en la Solución de los Reportes AEH Habitacionales ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + newValue + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + newValue + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraAEHChart');
            //meta   = Ext.getCmp('ChartSDDemoraAEHField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_aeh.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                    
    },
    
    filterSDDemoraAEHTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraAEH();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraAEHEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraAEHProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        var desde = Ext.getCmp('ChartDemSDAehStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemSDAehEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en la Solución de los Reportes ';
        }
        
        if(newValue == 'SD Comunes'){
            titulo = 'Demora Promedio en la Solución de los Reportes AEH Comunes ';
        }
        if(newValue == 'SD Habitaciones'){
            titulo = 'Demora Promedio en la Solución de los Reportes AEH Habitacionales ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + newValue,   
            frame  = Ext.getCmp('iframeSDDemoraAEHChart');
            //meta   = Ext.getCmp('ChartSDDemoraAEHField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_aeh.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                      
    },
    
    filterSDDemoraAEHRango: function() {
        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraAEH();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraAEHEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraAEHProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        var tipo = Ext.getCmp('viewSDDemoraAEHTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en la Solución de los Reportes AEH ';
        }
        
        var desde = Ext.getCmp('ChartDemSDAehStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemSDAehEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
                
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en la Solución de los Reportes AEH Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en la Solución de los Reportes AEH Habitacionales ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraAEHChart');
            //meta   = Ext.getCmp('ChartSDDemoraAEHField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_aeh.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                
    },
    
    //////////////////////////////////////////////////////////////////////
    
    filterSDDemoraNoAEHNoSumProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraNoAEHNoSum();
            
        viewPanel.setLoading('Cargando...');
                    
        var tipo = Ext.getCmp('viewSDDemoraNoAEHNoSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        var estado = Ext.getCmp('viewSDDemoraNoAEHNoSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConNoAehNoSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConNoAehNoSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/No AEH/No Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/No AEH/No Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + newValue;
        }

        var filtro = newValue + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraNoAEHNoSumChart');
            //meta   = Ext.getCmp('ChartSDDemoraNoAEHNoSumField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_noaeh_nosum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000);          
        }           
    },
    
    filterSDDemoraNoAEHNoSumEstado: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraNoAEHNoSum();
            
        viewPanel.setLoading('Cargando...');
        
        var tipo = Ext.getCmp('viewSDDemoraNoAEHNoSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraNoAEHNoSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConNoAehNoSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConNoAehNoSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/No AEH/No Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/No AEH/No Sumin ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + newValue + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + newValue + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraNoAEHNoSumChart');
            //meta   = Ext.getCmp('ChartSDDemoraNoAEHNoSumField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_noaeh_nosum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                    
    },
    
    filterSDDemoraNoAEHNoSumTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraNoAEHNoSum();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraNoAEHNoSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraNoAEHNoSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConNoAehNoSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConNoAehNoSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        if(newValue == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/No AEH/No Sumin ';
        }
        if(newValue == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/No AEH/No Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + newValue,   
            frame  = Ext.getCmp('iframeSDDemoraNoAEHNoSumChart');
            //meta   = Ext.getCmp('ChartSDDemoraNoAEHNoSumField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_noaeh_nosum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                      
    },
    
    filterSDDemoraNoAEHNoSumRango: function() {
        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraNoAEHNoSum();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraNoAEHNoSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraNoAEHNoSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        var tipo = Ext.getCmp('viewSDDemoraNoAEHNoSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/No AEH/No Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConNoAehNoSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConNoAehNoSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
                
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/No AEH/No Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/No AEH/No Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraNoAEHNoSumChart');
            //meta   = Ext.getCmp('ChartSDDemoraAEHField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_noaeh_nosum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                
    },
    
    ////////////////////////////////////////////////////////////////////
    
    filterSDDemoraAEHNoSumProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraAEHNoSum();
            
        viewPanel.setLoading('Cargando...');
                    
        var tipo = Ext.getCmp('viewSDDemoraAEHNoSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        var estado = Ext.getCmp('viewSDDemoraAEHNoSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConAehNoSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConAehNoSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/AEH/No Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/AEH/No Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + newValue;
        }

        var filtro = newValue + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraAEHNoSumChart');
            //meta   = Ext.getCmp('ChartSDDemoraAEHNoSumField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_aeh_nosum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000);          
        }           
    },
    
    filterSDDemoraAEHNoSumEstado: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraAEHNoSum();
            
        viewPanel.setLoading('Cargando...');
        
        var tipo = Ext.getCmp('viewSDDemoraAEHNoSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraAEHNoSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConAehNoSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConAehNoSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/AEH/No Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/AEH/No Sumin ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + newValue + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + newValue + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraAEHNoSumChart');
            //meta   = Ext.getCmp('ChartSDDemoraAEHNoSumField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_aeh_nosum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                    
    },
    
    filterSDDemoraAEHNoSumTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraAEHNoSum();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraAEHNoSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraAEHNoSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConAehNoSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConAehNoSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        if(newValue == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/AEH/No Sumin ';
        }
        if(newValue == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/AEH/No Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + newValue,   
            frame  = Ext.getCmp('iframeSDDemoraAEHNoSumChart');
            //meta   = Ext.getCmp('ChartSDDemoraAEHNoSumField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_aeh_nosum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                      
    },
    
    filterSDDemoraAEHNoSumRango: function() {
        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraAEHNoSum();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraAEHNoSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraAEHNoSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        var tipo = Ext.getCmp('viewSDDemoraAEHNoSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/AEH/No Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConAehNoSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConAehNoSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
                
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/AEH/No Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/AEH/No Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraAEHNoSumChart');
            //meta   = Ext.getCmp('ChartSDDemoraAEHField').text;
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_aeh_nosum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                
    },
    
    ///////////////////////////////////////////////////////////////////////
    
    filterSDDemoraConAEHSumProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraConAEHSum();
            
        viewPanel.setLoading('Cargando...');
                    
        var tipo = Ext.getCmp('viewSDDemoraConAEHSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        var estado = Ext.getCmp('viewSDDemoraConAEHSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConAehSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConAehSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/AEH/Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/AEH/Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + newValue;
        }

        var filtro = newValue + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraConAEHSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_aeh_sum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000);          
        }           
    },
    
    filterSDDemoraConAEHSumEstado: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraConAEHSum();
            
        viewPanel.setLoading('Cargando...');
        
        var tipo = Ext.getCmp('viewSDDemoraConAEHSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraConAEHSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConAehSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConAehSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/AEH/Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/AEH/Sumin ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + newValue + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + newValue + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraConAEHSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_aeh_sum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                    
    },
    
    filterSDDemoraConAEHSumTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraConAEHSum();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraConAEHSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraConAEHSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConAehSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConAehSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        if(newValue == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/AEH/Sumin ';
        }
        if(newValue == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/AEH/Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + newValue,   
            frame  = Ext.getCmp('iframeSDDemoraConAEHSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_aeh_sum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                      
    },
    
    filterSDDemoraConAEHSumRango: function() {
        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraConAEHSum();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraConAEHSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraConAEHSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        var tipo = Ext.getCmp('viewSDDemoraConAEHSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/AEH/Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConAehSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConAehSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
                
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/AEH/Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/AEH/Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraConAEHSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_aeh_sum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                
    },
    
    ///////////////////////////////////////////////////////////////////////////
    
    filterSDDemoraConNoAEHSumProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraConNoAEHSum();
            
        viewPanel.setLoading('Cargando...');
                    
        var tipo = Ext.getCmp('viewSDDemoraConNoAEHSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        var estado = Ext.getCmp('viewSDDemoraConNoAEHSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConNoAEHSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConNoAEHSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/No AEH/Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/No AEH/Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + newValue;
        }

        var filtro = newValue + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraConNoAEHSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_noaeh_sum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000);          
        }           
    },
    
    filterSDDemoraConNoAEHSumEstado: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraConNoAEHSum();
            
        viewPanel.setLoading('Cargando...');
        
        var tipo = Ext.getCmp('viewSDDemoraConNoAEHSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraConNoAEHSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConNoAEHSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConNoAEHSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/No AEH/Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/No AEH/Sumin ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + newValue + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + newValue + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraConNoAEHSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_noaeh_sum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                    
    },
    
    filterSDDemoraConNoAEHSumTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraConNoAEHSum();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraConNoAEHSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraConNoAEHSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConNoAEHSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConNoAEHSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        if(newValue == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/No AEH/Sumin ';
        }
        if(newValue == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/No AEH/Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + newValue,   
            frame  = Ext.getCmp('iframeSDDemoraConNoAEHSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_noaeh_sum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                      
    },
    
    filterSDDemoraConNoAEHSumRango: function() {
        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDemoraConNoAEHSum();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDemoraConNoAEHSumEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        var proyecto = Ext.getCmp('viewSDDemoraConNoAEHSumProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        var tipo = Ext.getCmp('viewSDDemoraConNoAEHSumTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Demora Promedio en Reportes Const/No AEH/Sumin ';
        }
        
        var desde = Ext.getCmp('ChartDemConNoAEHSumStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDemConNoAEHSumEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
                
        if(tipo == 'SD Comunes'){
            titulo = 'Demora Promedio en Reportes Comunes Const/No AEH/Sumin ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Demora Promedio en Reportes Habitacionales Const/No AEH/Sumin ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDemoraConNoAEHSumChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_demora_con_noaeh_sum.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                
    },
    
    ////////////////////////////////////////////////////////////////////
    
    filterSDDefectosProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDefectos();
            
        viewPanel.setLoading('Cargando...');
                    
        var tipo = Ext.getCmp('viewSDDefectosTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        var estado = Ext.getCmp('viewSDDefectosEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        var desde = Ext.getCmp('ChartDefectosStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDefectosEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD Habitacionales ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + newValue;
        }

        var filtro = newValue + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDefectosChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_defectos_reportes.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },4000);          
        }           
    },
    
    filterSDDefectosEstado: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDefectos();
            
        viewPanel.setLoading('Cargando...');
        
        var tipo = Ext.getCmp('viewSDDefectosTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        var proyecto = Ext.getCmp('viewSDDefectosProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        var desde = Ext.getCmp('ChartDefectosStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDefectosEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD Habitacionales ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + newValue + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + newValue + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDefectosChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_defectos_reportes.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },4000); 
        }                    
    },
    
    filterSDDefectosTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDefectos();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDefectosEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        var proyecto = Ext.getCmp('viewSDDefectosProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        var desde = Ext.getCmp('ChartDefectosStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDefectosEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        if(newValue == 'SD Comunes'){
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD Comunes ';
        }
        if(newValue == 'SD Habitaciones'){
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD Habitacionales ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + newValue,   
            frame  = Ext.getCmp('iframeSDDefectosChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_defectos_reportes.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },4000); 
        }                      
    },
    
    filterSDDefectosRango: function() {
        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDDefectos();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDDefectosEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        var proyecto = Ext.getCmp('viewSDDefectosProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        var tipo = Ext.getCmp('viewSDDefectosTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD ';
        }
        
        var desde = Ext.getCmp('ChartDefectosStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartDefectosEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
                
        if(tipo == 'SD Comunes'){
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Relación Demora/Tipo de Defecto/Reportes SD Habitacionales ';
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + ', ' + estado + ' ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDDefectosChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_defectos_reportes.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                
    },
    
    /////////////////////////////////////////////////////////////////////
    
    filterSDNoProcedenProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDNoProceden();
            
        viewPanel.setLoading('Cargando...');
                    
        var tipo = Ext.getCmp('viewSDNoProcedenTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Comportamiento de las SD que No Proceden ';
        }
        
        var desde = Ext.getCmp('ChartSDNoProcedenStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartSDNoProcedenEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
                
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Comportamiento de las SD que No Proceden ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Comportamiento de las SD que No Proceden Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Comportamiento de las SD que No Proceden Habitacionales ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + newValue;
        }

        var filtro = newValue + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDNoProcedenChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_noproceden.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000);          
        }           
    },
        
    filterSDNoProcedenTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDNoProceden();
            
        viewPanel.setLoading('Cargando...');
        
        var proyecto = Ext.getCmp('viewSDNoProcedenProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Comportamiento de las SD que No Proceden ';
        }
        var desde = Ext.getCmp('ChartSDNoProcedenStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartSDNoProcedenEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Comportamiento de las SD que No Proceden ';
        }
        
        if(newValue == 'SD Comunes'){
            titulo = 'Comportamiento de las SD que No Proceden Comunes ';
        }
        if(newValue == 'SD Habitaciones'){
            titulo = 'Comportamiento de las SD que No Proceden Habitacionales ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + newValue,   
            frame  = Ext.getCmp('iframeSDNoProcedenChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_noproceden.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                      
    },
    
    filterSDNoProcedenRango: function() {
        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDNoProceden();
            
        viewPanel.setLoading('Cargando...');
        
        var proyecto = Ext.getCmp('viewSDNoProcedenProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Comportamiento de las SD que No Proceden ';
        }
        
        var tipo = Ext.getCmp('viewSDNoProcedenTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Comportamiento de las SD que No Proceden ';
        }
        
        var desde = Ext.getCmp('ChartSDNoProcedenStart').getValue();
        if (desde === null) {
            desde = 'Inicio';
        }
        
        var hasta = Ext.getCmp('ChartSDNoProcedenEnd').getValue();
        if (hasta === null) {
            hasta = 'Final';
        }
                
        if(tipo == 'SD Comunes'){
            titulo = 'Comportamiento de las SD que No Proceden Comunes ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Comportamiento de las SD que No Proceden Habitacionales ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ', ' + proyecto + ' ';
        }

        var filtro = proyecto + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDNoProcedenChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_noproceden.php?listar='+filtro+'&titulo='+ titulo+'&desde='+desde+'&hasta='+hasta;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                
    },
    
    ////////////////////////////////////////////////////////////////////////////
    
    filterSDComparativaProyect: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDComparativa();
            
        viewPanel.setLoading('Cargando...');
                    
        var tipo = Ext.getCmp('viewSDComparativaTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Comparativa de SD entre Proyectos ';
        }
        
        var estado = Ext.getCmp('viewSDComparativaEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Comparativa de SD entre Proyectos ';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Comparativa de SD entre Proyectos ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Comparativa de SD Comunes entre Proyectos ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Comparativa de SD Habitacionales entre Proyectos ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ' (' + newValue + ') ';
            Ext.getCmp('viewSDComparativaEstado').setDisabled(false);
            Ext.getCmp('viewSDComparativaTipo').setDisabled(false);
        }
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + '. ' + estado;
        }

        var filtro = newValue + '.' + estado + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDComparativaChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_comparativa.php?listar='+filtro+'&titulo='+ titulo;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000);          
        }           
    },
    
    filterSDComparativaEstado: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDComparativa();
            
        viewPanel.setLoading('Cargando...');
        
        var tipo = Ext.getCmp('viewSDComparativaTipo').value;
        if (tipo === null) {
            tipo   = 'Todas';
            titulo = 'Comparativa de SD entre Proyectos ';
        }
        
        var proyecto = Ext.getCmp('viewSDComparativaProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Comparativa de SD entre Proyectos ';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Comparativa de SD entre Proyectos ';
        }
        
        if(tipo == 'SD Comunes'){
            titulo = 'Comparativa de SD Comunes entre Proyectos ';
        }
        if(tipo == 'SD Habitaciones'){
            titulo = 'Comparativa de SD Habitacionales entre Proyectos ';
        }
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ' (' + proyecto + ') ';
        }
        if(newValue !== null && newValue.length > 0 && newValue != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + '. ' + newValue;
        }

        var filtro = proyecto + '.' + newValue + '.' + tipo,   
            frame  = Ext.getCmp('iframeSDComparativaChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_comparativa.php?listar='+filtro+'&titulo='+ titulo;
            window.setTimeout(function(){ viewPanel.setLoading(false); },2000); 
        }                    
    },
    
    filterSDComparativaTipo: function(combo, newValue) {

        // Titulo de la grafica
        var titulo    = '',
            viewPanel = this.getMainPanelChartSDComparativa();
            
        viewPanel.setLoading('Cargando...');
        
        var estado = Ext.getCmp('viewSDComparativaEstado').value;
        if (estado === null) {
            estado = 'Todos';
            titulo = 'Comparativa de SD entre Proyectos ';
        }
        
        var proyecto = Ext.getCmp('viewSDComparativaProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
            titulo = 'Comparativa de SD entre Proyectos ';
        }
        
        if (newValue === null) {
            newValue = 'Todos';
            titulo   = 'Comparativa de SD entre Proyectos ';
        }
        
        if(newValue == 'SD Comunes'){
            titulo = 'Comparativa de SD Comunes entre Proyectos ';
        }
        
        if(newValue == 'SD Habitaciones'){
            titulo = 'Comparativa de SD Habitacionales entre Proyectos ';
        }
        
        if(proyecto !== null && proyecto.length > 0 && proyecto != 'Todos'){
            var title_length_proyecto = titulo.length;
            titulo = titulo.substring(0,title_length_proyecto - 1) + ' (' + proyecto + ') ';
        }
        
        if(estado !== null && estado.length > 0 && estado != 'Todos'){
            var title_length_estado = titulo.length;
            titulo = titulo.substring(0,title_length_estado - 1) + '. ' + estado;
        }

        var filtro = proyecto + '.' + estado + '.' + newValue,   
            frame  = Ext.getCmp('iframeSDComparativaChart');
            
        if(frame && frame.rendered ){
            let ipserver = localStorage.getItem('ipserver');
            frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/graficas/sd_comparativa.php?listar='+filtro+'&titulo='+ titulo;
            window.setTimeout(function(){ viewPanel.setLoading(false); },4000); 
        }                      
    },
    
    ///////////////////////////////////////////////////////////////////////
    
    ChartSDDemoraMetaKey: function(field, e){
        if (e.getKey() === e.ENTER) {
            this.ChartSDDemoraMeta();
        }        
    },
    
    ChartSDDemoraMeta: function(){
        
        var meta   = Ext.getCmp('ChartSDDemoraField').getValue(),
            frame  = Ext.getCmp('iframeSDDemoraChart'),
            src    = frame.getEl().dom.src,
            posstr = src.indexOf('meta'),
            substr = src.substring(0,posstr);
            
        Ext.Ajax.request({ //dispara la petición

            url: './php/garantia/SdActions.php',
            method: 'POST',
            params: {accion: 'ActualizarMetaSDDemora', meta: meta},
            success: function(result, request) {
                var jsonData = Ext.JSON.decode(result.responseText);
                if (jsonData.failure) {

                    Ext.MessageBox.show({
                        title: 'Mensaje del Sistema',
                        msg: jsonData.message,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
                else {
                    if(frame && frame.rendered ){
                        frame.getEl().dom.src = substr +'meta='+ meta;
                    }
                }
            },
            failure: function() {

                Ext.MessageBox.show({
                    title: 'Mensaje del Sistema',
                    msg: 'Ha ocurrido un error en el Sistema. Por favor, vuelva a intentar realizar la operacion, de continuar el problema consulte al Administrador del Sistema.',
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });
    },
    
    /*************************************************
     ********      FIN REPORTES, GRAFICAS     ********
     *************************************************/


});