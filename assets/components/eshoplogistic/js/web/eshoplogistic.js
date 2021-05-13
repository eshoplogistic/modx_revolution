window.onload = function() {

    let esl = {
        items: {
            getCostResult: {
                totalCost: 'esl_cost',
                cartCost: 'esl-cart-cost',
                deliveryCost: 'esl-delivery-cost',
                deliveryTime: 'esl-delivery-time',
                deliveryMode: 'esl-delivery-mode',
                addressTitle: 'esl-address',
                fieldTerminal: 'esl-address-field-terminal',
                fieldStreet: 'esl-address-field-street',
                fieldBuilding: 'esl-address-field-building',
                fieldRoom: 'esl-address-field-room',
                paymentNote:  'esl-payment-note'
            },
            loader: {
                img: eShopLogisticConfig.assetsUrl+'css/web/loader.svg',
                deliveryBlocks: 'esl-deliveries',
                loader: 'esl-order-block-loading'
            },
            activeDelivery: false,
            countMode: {terminal:0, door:0}
        },
        loader: function (el=null) {
            let loader_img = '<img src="'+this.items.loader.img+'">'
            if(!el) {
                let _fv = document.getElementsByClassName('esl-loader')
                Array.prototype.filter.call(_fv, function(_fv){
                    _fv.innerHTML = loader_img;
                })
            }
            else {
                el.innerHTML = loader_img;
            }
            
            let ditm = document.querySelectorAll('.'+this.items.loader.deliveryBlocks)
            
            for (let item of ditm) {
                item.classList.add('hidden'); 
            }
        },
        msGetCost: function (data) {

            if(!data.serviceName) {
                let pvz = document.querySelector('.esl-delivery-address')
                pvz.closest('.esl-total-item').classList.add('hidden');
            }
            
            for (const [key, value] of Object.entries(this.items.getCostResult)) {
                
                let _fv = document.getElementsByClassName(value)
                    
                Array.prototype.filter.call(_fv, function(_fv) {
                    
                    _flclst = _fv.closest('.esl-total-item')
                    
                    if(data[key]) {

                        let crr = '' 
                        if(key == 'deliveryCost') {
                            if(Number(data[key]) > 0) {
                                crr = ' руб.'  //+data.delivery.currency
                            }
                        }
                        
                        if(_flclst) {
                            _flclst.classList.remove('hidden'); 
                        }
                        
                        _fv.textContent = data[key]+crr;
                    }
                    else {
                        
                        if(key == 'deliveryCost' || key == 'deliveryMode') {
                            if(_flclst) {
                                _flclst.classList.add('hidden'); 
                            }
                        }
                                                
                        switch(key) {
                            
                            case 'addressTitle':  
                                if(data.serviceName) {
                                    _fv.innerHTML = _fv.dataset[data.delivery.mode]+' '+data.serviceName 
                                }
                                break

                            case 'deliveryTime':
                                if(!data.serviceName) {
                                    _flclst.classList.add('hidden');
                                }
                                break

                            case 'fieldTerminal':
                                if(data.serviceName) {
                                    if (data.delivery.mode == 'terminal') {
                                        _fv.classList.remove('hidden');
                                    } else {
                                        _fv.classList.add('hidden');
                                    }
                                }
                                break
                                
                            case 'paymentNote': 
                                let _fvp = document.getElementsByClassName(value)
                                Array.prototype.filter.call(_fvp, function(_fvp){
                                    _fvp.classList.add('hidden')
                                })
                                let payments_notes = document.querySelectorAll('.'+value+'-'+data.delivery.service)
                                for (let item of payments_notes) {
                                    item.classList.remove('hidden'); 
                                }
                                break
            
                            case 'fieldStreet':
                            case 'fieldBuilding':
                            case 'fieldRoom':
                                if(data.delivery.mode == 'terminal') { _fv.classList.add('hidden'); }
                                else { _fv.classList.remove('hidden'); }
                                break
                                
                            case 'totalCost':
                                _fv.textContent = data.costf 
                                break;

                        }
                    }
                    
                })
            }
            
            if(data.delivery.target) {
                let city = document.getElementById('city'),
                    fias = document.getElementById('fias')
                if(city) {
                    city.value = data.delivery.target;
                }
                if(fias) {
                    fias.value = data.delivery.fias;
                }
            }
            
 
        },
        eslGetDeliveries: function () {

            var _self = this
            
            this.items.countMode = {"terminal":"0", "door":"0"}
            this.items.activeDelivery = false
            
            async function processData(_data, cont, _fv) {
                await _self.eslGetDelivery(_data, cont, _fv)
            }
            
            async function process() {
                
                let _fv = document.getElementsByName('delivery'),
                    delivery_empty_block = document.getElementById('esl-delivery-empty'),
                    current_delivery = document.querySelector('input[name=delivery]:checked').id,
                    payment = document.querySelector('input[name=payment]:checked').value,
                    fias = document.querySelector('input[name=fias]').value,
                    terminal_list = document.getElementById('esl-deliveries-terminal'),
                    door_list = document.getElementById('esl-deliveries-door'),
                    loader = document.getElementById(_self.items.loader.loader)
                    
                loader.classList.remove('hidden')

                for (const item of _fv) {
                    
                    let _data = {
                        payment: payment,
                        service: item.dataset.service,
                        mode: item.dataset.mode,
                        fias: fias
                    },
                    cont = document.getElementById(_data.service+'-'+_data.mode)

                    if(cont !== null) {
                        await processData(_data, cont, item)
                    }
                }
                
                
                if(_self.items.countMode.terminal == 0 && _self.items.countMode.door == 0) {
                    delivery_empty_block.classList.remove('hidden');
                }
                else {
                    delivery_empty_block.classList.add('hidden');
                    
                    if(_self.items.countMode.terminal > 0) {
                        terminal_list.classList.remove('hidden');  
                    }
                    else {
                        terminal_list.classList.add('hidden'); 
                    }

                    if(_self.items.countMode.door > 0) {
                        door_list.classList.remove('hidden');  
                    }
                    else {
                        door_list.classList.add('hidden'); 
                    }
                }
                
                

                if(!_self.items.activeDelivery) {
                    _self.items.activeDelivery = delivery_empty_block.querySelector('input[name="delivery"]').id
                }
                

                if(current_delivery == _self.items.activeDelivery) {
                    miniShop2.Order.getcost()
                }
                else {
                    document.getElementById(_self.items.activeDelivery).click()
                }
                
                loader.classList.add('hidden')

            }

            process()
            
        }, 
        eslGetDelivery: function (_data, cont, _fv) {
            
            var _self = this
            
            let responseReq = async function sendRequest () {
                return await $.ajax({
                    url: eShopLogisticConfig.actionUrl,
                    type: 'POST',
                    data: _data,
                    dataType: 'json',
                    timeout: 5000
                });
            }

            return responseReq().then(response => {

                if(typeof response.service != 'undefined') {
                           
                    if(!_self.items.activeDelivery) {
                        _self.items.activeDelivery = _fv.id
                    }
                    
                    if(typeof response.mode != 'undefined') {
                        _self.items.countMode[response.mode]++
                    }
                
                    let cl = (response.is_free == 1) ? ' class="esl-price-free"' : '', 
                        html = '<span class="esl-price">Стоимость: <span'+cl+'>'+response.price+'</span> '+response.currency+'</span>'
                              +'<span class="esl-time">Срок: <span>'+response.time+'</span></span>'

                    if(response.note !== null) {
                        html += '<span class="esl-note"><span>'+response.note+'</span></span>'
                    }

                    if(typeof response.terminals != 'undefined') {
                        if(response.terminals != '') {
                            html += "<span class='els-terminals'><a href='#' data-terminals='"+JSON.stringify(response.terminals)+"'>Выбрать пункт самовывоза</a></span>"
                        }
                    }

                    cont.innerHTML = html

                    _fv.closest('.esl-block').classList.remove('hidden'); 
                }
                else {
                    _fv.closest('.esl-block').classList.add('hidden'); 
                }

            }).catch(response => {
                _fv.closest('.esl-block').classList.add('hidden'); 
            });
            
        },
        search: function (text) {

            _self = this
            
            const request = new XMLHttpRequest()
            request.open('POST', eShopLogisticConfig.actionUrl, true)
            request.responseType = 'json'
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
            request.send('text='+text)
            
            request.addEventListener("readystatechange", () => {
                
                let html = '',
                    block = document.getElementById('esl-address-result')

                if (request.readyState === 4 && request.status === 200) {
                    for( let item in request.response ){
                        let obj = request.response[item]
                        html += '<a href="#" data-fias="'+obj.fias+'" data-city="'+obj.target+'" data-index="'+obj.index+'">'+obj.target+'</a>'
                    }
                    block.innerHTML = html
                    block.style = (html !== '') ? 'display: block' : 'display: none'
                }
                
                
                let cityLinks = document.querySelectorAll('#esl-address-result a')
                
                cityLinks.forEach.call(cityLinks, function(el){
                    el.addEventListener('click', event => {
                        event.preventDefault()
                        _self.loader()
                        block.innerHTML = ''
        
                        let fields = ['fias','index','city']

                        fields.forEach(elem => {
                            let field = document.getElementById(elem)
                            if(field) {
                                field.value = el.dataset[elem]
                                miniShop2.Order.add(elem, el.dataset[elem])
                            }
                        });
                        
                        _self.eslGetDeliveries()
                                                
                        block.style = 'display: none'
                    });
                })
                
            })

        },
        setAddress: function (address=false) {
            
            let nhd = document.querySelectorAll('.esl-address-field-terminal, .esl-address'),
                alink = document.querySelectorAll('.els-terminals a'),
                terminal = document.querySelector('input[name="terminal"]'),
                current =  document.querySelector('input[name=delivery]:checked'),
                ainfo = document.querySelector('.esl-delivery-address')

            if(!address) {
                
                terminal.value = ''

                if(ainfo) {
                    ainfo.textContent = ''
                    ainfo.closest('.esl-total-item').classList.add('hidden'); 
                }
                
                for (let item of alink) {
                    item.textContent = 'Выбрать пункт самовывоза'
                }
                
                for (let item of nhd) {
                    item.classList.add('hidden'); 
                }
                
            }  
            else {
                
                for (let item of nhd) {
                    item.classList.remove('hidden'); 
                }

                for (let item of alink) {
                    item.textContent = 'Выбрать пункт самовывоза'
                }

                terminal.value = address

                if(ainfo) {
                    ainfo.textContent = address
                    ainfo.closest('.esl-total-item').classList.remove('hidden');
                }

                let current_ainfo = current.closest('.esl-block')
                if(current_ainfo) {
                    current_ainfo.querySelector('.els-terminals a').textContent = 'Изменить пункт самовывоза'
                }
                
            }
        },
        reload:  function () {
        
            this.loader() 
            
            let _self = this

            async function process() {
                await _self.eslGetDeliveries()
            }

            async function run() {
                await process()
                miniShop2.Order.getcost()
            }

            run()
        }
    }
    
    
    miniShop2.Callbacks.add('Cart.change.response.success', 'eShopLogisticCartChange', function (response) {
        esl.reload()
    })
    
    
    miniShop2.Callbacks.add('Cart.remove.response.success', 'eShopLogisticCartRemove', function (response) {
        esl.reload()
    })

    miniShop2.Callbacks.add('Order.getcost.response.success', 'eShopLogisticGetCost', function (response) {
        if(typeof response.data.delivery == 'object') {
            esl.msGetCost(response.data)
        }
    })

    miniShop2.Callbacks.add('Order.add.response.success', 'eShopLogisticAdd', function (response) {
        if(typeof response.data.delivery == 'string') {
            esl.setAddress()
        }
        
        if(eShopLogisticConfig.payment_on == 1) {
            if(typeof response.data.payment == 'string') {
                esl.loader()
                esl.eslGetDeliveries()
            }
        }
    })
    
        
        
    document.getElementById('city').oninput = function() { 
        if(this.value.length >= 3) {
            esl.search(this.value)
        }
    };
    
    
    let deliveryBlocks = document.querySelectorAll('.esl-block')
    deliveryBlocks.forEach.call(deliveryBlocks, function(el){
        el.addEventListener('click', event => {
            el.querySelector('[name=delivery]').dispatchEvent(new Event('click'))
        })
    })
    

    esl.loader()
    esl.eslGetDeliveries()
    

    
    let ID_MODAL = 'esl_yandex_map',
        YANDEX_MAP_CONTAINER_ID = 'esl_yandex_map_container',
        YANDEX_MAP_CONTAINER_ID_FOR_MAP = 'esl-modal-yandex-map-wrap',
        YANDEX_MAP_CONTAINER_ID_FOR_ADDRESS = 'esl-modal-yandex-map-address'


    let modal = {
        backdrop: null,
        initLayout: {
          createRoot: function (){
              let root = document.createElement('div')
              root.classList.add('modal','fade')
              root.setAttribute('tabindex','-1')
              root.setAttribute('role','dialog')
              root.setAttribute('id', ID_MODAL)
              document.body.appendChild(root)
              return root;
          },
          createDialog: function (dom){
              let dialog = document.createElement('div')
              dialog.classList.add('modal-dialog','modal-lg')
              if(document.documentElement.clientWidth < 768){
                  dialog.setAttribute('style','width: 100%')
              }
              dom.appendChild(dialog)
              return dialog;
          },
          createContent: function (dom){
              let content = document.createElement('div')
              content.classList.add('modal-content')
              dom.appendChild(content)
              return content;
          },
          createHeader: function (dom){
              let header = document.createElement('div')
              header.classList.add('modal-header')
              header.innerHTML = '<h4>Пункты самовывоза</h4>'
              dom.appendChild(header)
              return header;
          },
          createButtonClose: function (dom){
              let buttonClose = document.createElement('button')
              buttonClose.setAttribute('type','button')
              buttonClose.setAttribute('data-dismiss','modal')
              buttonClose.setAttribute('aria-label','Close')
              buttonClose.classList.add('close')
              buttonClose.addEventListener('click', modal.close)
              dom.appendChild(buttonClose)
              return buttonClose;
          },
          createIconClose: function (dom){
              let iconClose = document.createElement('span')
              iconClose.setAttribute('aria-hidden','true')
              iconClose.innerHTML= '&times;'
              dom.appendChild(iconClose)
              return iconClose;
          },
          createBody: function (dom){
              let body = document.createElement('div')
              body.classList.add('modal-body')
              dom.appendChild(body)
              return body;
          },
          createRow: function (dom){
              let row = document.createElement('div')
              row.classList.add('class','row')
              dom.appendChild(row)
              return row;
          },
          createColForMap: function (dom){
              let col = document.createElement('div')
              col.classList.add('col-lg-12','col-md-12')
              col.setAttribute('id',YANDEX_MAP_CONTAINER_ID_FOR_MAP)
              dom.appendChild(col)
              return
          },
            createBackDrop: function (){
                let backdrop = document.createElement('div')
                backdrop.classList.add('modal-backdrop','fade')
                backdrop.setAttribute('style', 'display: none')
                document.body.appendChild(backdrop)
                modal.backdrop = backdrop
            },
        },
        createModalBootstrap: function (){
            if(this.checkOnInit()) return;
            let root = this.initLayout.createRoot(),
                dialog = this.initLayout.createDialog(root),
                content = this.initLayout.createContent(dialog),
                header = this.initLayout.createHeader(content),
                buttonClose = this.initLayout.createButtonClose(header),
                iconClose = this.initLayout.createIconClose(buttonClose),
                body = this.initLayout.createBody(content),
                row = this.initLayout.createRow(body),
                colMap = this.initLayout.createColForMap(row);

            this.initLayout.createBackDrop()
        },
        checkOnInit: function (){
            if(document.getElementById(this.idModal)){
                return true
            }
            return false
        },

        open: function (){
            let modalLocal = document.getElementById(ID_MODAL);
            document.body.classList.add('modal-open')
            document.body.setAttribute('style','padding-right:17px')
            modal.backdrop.removeAttribute('style')

            setTimeout(() => {
                modalLocal.setAttribute('style','display:block;padding-left:17px')
                setTimeout(() => {
                    modalLocal.classList.add('show')
                    modal.backdrop.classList.add('show')
                },200)
            },100)

        },
        close: function (){
            let modalLocal = document.getElementById(ID_MODAL);

            modalLocal.classList.remove('show')
            setTimeout(() => {
                modal.backdrop.classList.remove('show')
                setTimeout(() => {
                    document.body.classList.remove('modal-open')
                    document.body.removeAttribute('style')
                    modal.backdrop.setAttribute('style', 'display: none')
                    modalLocal.removeAttribute('style')
                    document.dispatchEvent(new CustomEvent('eshoplogistic.hide.modal'))
                },200)
            },100)

        },
        destroy: function (){
            if(this.checkOnInit()){
                document.getElementById(this.idModal).remove()
            }
        }
    }
    modal.createModalBootstrap()


    let yandexMaps = {
        terminals: [],
        settings: {},
        initApi: function (){
            let script = document.createElement('script')
            script.setAttribute('src', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU')
            script.setAttribute('defer','')
            document.head.appendChild(script)
        },
        createContainer: function (){
            let container = document.createElement('div'),
                modalBody = document.getElementById(ID_MODAL).querySelector('.modal-body  #'+YANDEX_MAP_CONTAINER_ID_FOR_MAP);
            container.setAttribute('id',YANDEX_MAP_CONTAINER_ID)
            container.setAttribute('style','width: 100%;height:400px')
            modalBody.appendChild(container)
        },
        initMap: function (event){
            var map = new ymaps.Map(YANDEX_MAP_CONTAINER_ID, {
                center: [yandexMaps.terminals[0]['lat'],yandexMaps.terminals[0]['lon']],
                zoom: 10,
                controls: ['zoomControl']
            }, {
                suppressMapOpenBlock: true
            })
            map.behaviors.disable('scrollZoom')
            yandexMaps.createPlacemarks(yandexMaps.terminals, map)
        },
        createPlacemarks: function (items, map){
            let geoObjects = [],
                iteration = 0;
            for(const terminal of items){
                if(terminal.lat === '' || terminal.lon === ''){
                    continue;
                }
                const BalloonContentLayout = ymaps.templateLayoutFactory.createClass(
                    `<h3 style="font-size: 1.3em;font-weight: bold;margin-bottom: 0.5em;">{{ properties.address }}</h3><p>{{ properties.note }}</p><p><b>Время работы: {{ properties.timeWork }}</b></p><button type="button" data-accept-terminal class="btn btn-success">Забрать отсюда</button>`,
                    {
                        build: function (){
                            BalloonContentLayout.superclass.build.call(this)
                            const button =  document.getElementById(YANDEX_MAP_CONTAINER_ID_FOR_MAP).querySelector('[data-accept-terminal]')
                            if(button){
                                button.addEventListener('click', this.selectedTerminal)
                            }

                        },
                        clear: function(){
                            const button =  document.getElementById(YANDEX_MAP_CONTAINER_ID_FOR_MAP).querySelector('[data-accept-terminal]')
                            if(button){
                                button.removeEventListener('click', this.selectedTerminal)
                            }
                            BalloonContentLayout.superclass.clear.call(this);
                        },
                        selectedTerminal: function (){
                            document.dispatchEvent(new CustomEvent('onSelectAddress', {detail: terminal}))
                            modal.close()
                        }
                    }
                )
                geoObjects[iteration] = new ymaps.Placemark([terminal.lat, terminal.lon],{
                    note: terminal.note,
                    address: terminal.address,
                    timeWork: terminal.workTime ? terminal.workTime : 'Не указано',
                },{
                    balloonContentLayout: BalloonContentLayout,
                    balloonPanelMaxMapArea: 0
                });
                iteration++;
            }
            let myClusterer = new ymaps.Clusterer(
                {clusterDisableClickZoom: false}
            );
            myClusterer.add(geoObjects);
            map.geoObjects.add(myClusterer);

        },
        destroyMap: function (){
            document.getElementById(YANDEX_MAP_CONTAINER_ID).remove()
        }
    }
    yandexMaps.initApi()

    let bindEvents = {
        clickOnTerminals: function (event){
            let parent = event.target.closest('.esl-block')
            if(parent){
                parent.querySelector('input').click()
            }
            if(event.target.closest('.els-terminals')){
                event.preventDefault();
                let terminals = event.target.getAttribute('data-terminals')
                if(terminals){
                    yandexMaps.createContainer()
                    yandexMaps.terminals = JSON.parse(terminals)
                    ymaps.ready(yandexMaps.initMap)
                    modal.open()
                }
            }
        },
        onCloseModal: function (){
            yandexMaps.destroyMap()
        }
    }
    
    document.addEventListener('click', bindEvents.clickOnTerminals, false);

    document.addEventListener('eshoplogistic.hide.modal', bindEvents.onCloseModal);
    
    document.addEventListener('onSelectAddress', function (event){
        esl.setAddress(event.detail.address)
    });
    
};