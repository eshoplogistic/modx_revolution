{set $services = '!eShopLogisticOrder' | snippet}

<form class="ms2_form" id="msOrder" method="post">
    
    <div class="row">
        <div class="col-12 col-md-4 esl-order-block">
            <h4>{'ms2_frontend_credentials' | lexicon}:</h4>
            {foreach ['email','receiver','phone'] as $field}
                <div class="form-group row input-parent">
                    <label class="col-md-4 col-form-label" for="{$field}">
                        {('ms2_frontend_' ~ $field) | lexicon} <span class="required-star">*</span>
                    </label>
                    <div class="col-md-8">
                        <input type="text" id="{$field}" placeholder="{('ms2_frontend_' ~ $field) | lexicon}"
                               name="{$field}" value="{$form[$field]}"
                               class="form-control{($field in list $errors) ? ' error' : ''}">
                    </div>
                </div>
            {/foreach}

            <div class="form-group row input-parent">
                <label class="col-md-4 col-form-label" for="comment">
                    {'ms2_frontend_comment' | lexicon} <span class="required-star">*</span>
                </label>
                <div class="col-md-8">
                    <textarea name="comment" id="comment" placeholder="{'ms2_frontend_comment' | lexicon}"
                              class="form-control{('comment' in list $errors) ? ' error' : ''}">{$form.comment}</textarea>
                </div>
            </div>
        </div>
                                
        <div class="col-12 col-md-5 esl-order-block">
            <h4>{'ms2_frontend_address' | lexicon}:</h4>
            
            {foreach ['index', 'city','terminal', 'street', 'building', 'room'] as $field}
            
                {if $field == 'terminal'}
                    {set $plsh = 'eshoplogistic_frontend_terminal' | lexicon}
                {else}
                    {set $plsh = ('ms2_frontend_' ~ $field) | lexicon}
                {/if}
            
                <div class="form-group row input-parent esl-address-field-{$field}{if $field == 'terminal'} hidden{/if}">
                    <label class="col-md-4 col-form-label" for="{$field}">
                        {$plsh} <span class="required-star">*</span>
                    </label>
                    <div class="col-md-8">
                        <input {if $field == 'city'}autocomplete="off"{/if} type="text" id="{$field}" placeholder="{$plsh}"
                               name="{$field}" 
                               value="{if $field == 'terminal'}{$order.terminal}{else}{$form[$field]}{/if}"
                               class="form-control{($field in list $errors) ? ' error' : ''}">
                               
                        {if $field == 'city'}
                            <input type="hidden" id="fias" name="fias" value="">
                            <div id="esl-address-result" style="display: none;"></div>
                        {/if}
                    </div>
                </div>
            {/foreach}

        </div> 

        <div class="col-12 col-md-3 esl-order-block" id="payments">
            <h4>Способ оплаты:</h4>
            <div class="form-group row">
                <div class="col-12">
                    {foreach $payments as $payment index=$index}
                        {var $checked = !($order.payment in keys $payments) && $index == 0 || $payment.id == $order.payment}
                        <div class="checkbox">
                            <label class="col-form-label payment input-parent">
                                <input type="radio" name="payment" value="{$payment.id}" id="payment_{$payment.id}"{$checked ? 'checked' : ''}>
                                {if $payment.logo?}
                                    <img src="{$payment.logo}" alt="{$payment.name}" title="{$payment.name}" class="mw-100"/>
                                {else}
                                    {$payment.name}
                                {/if}
                                {if $payment.description?}
                                    <p class="small">{$payment.description}</p>
                                {/if}
                                
                                {foreach $services.payments_comments[$payment.id] as $item}
                                    {if $item.value?}
                                        <span class="hidden esl-payment-note esl-payment-note-{$item.service}">{$item.value}</span>
                                    {/if}
                                {/foreach}
                            </label>
                        </div>

                    {/foreach}
                </div>
            </div>
        </div>
    </div>

    <div class="row" >
    
        <div id="deliveries" class="esl-order-block">
            
            <div id="esl-order-block-loading">
                <img src="assets/components/eshoplogistic/css/web/loader.svg">
                <p>Подождите, идёт загрузка информации о доставке...</p>
            </div>
            
            {set $nd = 'eshoplogistic_no_delivery_id' | option}
            
            {if $nd?}
                <div class="row esl-deliveries" id="esl-delivery-empty">
                    <div class="col-12">
                        <h4>Доставка</h4>
                        
                        {set $dflt = $deliveries.$nd}
                        
                        {if $dflt?}
                            <div class="checkbox">
                                <label class="col-form-label delivery input-parent">
                                    <input type="radio" name="delivery" value="{$dflt.id}" id="delivery_{$dflt.id}" data-payments="{$dflt.payments | json_encode}" checked>
                                    {if $dflt.logo?}
                                        <img src="{$dflt.logo}" alt="{$dflt.name}" title="{$dflt.name}"/>
                                    {else}
                                        {$dflt.name}
                                    {/if}
                                    {if $dflt.description?}
                                        <p class="small">
                                            {$dflt.description}
                                        </p>
                                    {/if}
                                </label>
                            </div>
                        {/if}
                        
                    </div>
                </div>
            {/if}
                     
            <div class="row esl-deliveries hidden" id="esl-deliveries-terminal">
                
                <div class="col-12">
                    <h4>Доставка до пункта выдачи</h4>
                    <div class="esl-delivery-container">

                        {foreach $deliveries as $delivery index=$index}
                        
                            {if $delivery.properties.mode != 'terminal'}
                                {continue}
                            {/if}
                            
                            <div class="checkbox esl-block">
                                
                                {if $services[$delivery.properties.service]['comment']?}
                                    
                                    <div class="esl-comment" data-toggle="tooltip" data-placement="top" title="{$services[$delivery.properties.service]['comment']}" >
                                        <svg class="bi bi-info-circle-fill text-success" width="20" height="20" viewBox="0 0 20 20" 
                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 16A8 8 0 108 0a8 8 0 000 16zm.93-9.412l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 100-2 1 1 0 000 2z"/>
                                        </svg>
                                    </div>
                                    
                                {/if}
                                
                                {set $name = $delivery.name | replace : 'Самовывоз ' : ''}
                                
                                <label class="col-form-label delivery input-parent">
                                    <input type="radio" name="delivery" value="{$delivery.id}" 
                                        data-mode="terminal" 
                                        data-service="{$delivery.properties.service}" 
                                        data-service-name="{$name}" 
                                        id="delivery_{$delivery.id}" 
                                        data-payments="{if $services[$delivery.properties.service]['payments']?}{$services[$delivery.properties.service]['payments']}{else}{$delivery.payments | json_encode}{/if}">
                                    {*if $delivery.logo?}
                                        <img src="{$delivery.logo}" class="esl-{$delivery.properties.service}" alt="{$delivery.name}" title="{$delivery.name}"/>
                                    {/if*}
    
                                    {$name}
    
                                    {if $delivery.description?}
                                        <p class="small">{$delivery.description}</p>
                                    {/if}
                                </label>
                                <div id="{$delivery.properties.service}-{$delivery.properties.mode}"></div>
                            </div>
                        {/foreach}
                        
                    </div>
                </div>
            </div>

            <div class="row esl-deliveries hidden" id="esl-deliveries-door">
                <div class="col-12">
                    <h4>Доставка до адреса (курьер)</h4>
                    <div class="esl-delivery-container">
                        {foreach $deliveries as $delivery index=$index}
                            {if $delivery.properties.mode != 'door'}
                                {continue}
                            {/if}
                            <div class="checkbox esl-block">
                                
                                {if $services[$delivery.properties.service]['comment']?}
                                    <div class="esl-comment" data-toggle="tooltip" data-placement="top" title="{$services[$delivery.properties.service]['comment']}" >
                                        <svg class="bi bi-info-circle-fill text-success" width="20" height="20" viewBox="0 0 20 20" 
                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 16A8 8 0 108 0a8 8 0 000 16zm.93-9.412l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 100-2 1 1 0 000 2z"/>
                                        </svg>
                                    </div>
                                {/if}
                                
                                {set $name = $delivery.name | replace : 'Курьер ' : ''}
                                
                                <label class="col-form-label delivery input-parent">
                                    <input type="radio"  data-mode="door" 
                                        data-service-name="{$name}" 
                                        data-service="{$delivery.properties.service}" 
                                        name="delivery" value="{$delivery.id}" id="delivery_{$delivery.id}"
                                        data-payments="{if $services[$delivery.properties.service]['payments']?}{$services[$delivery.properties.service]['payments']}{else}{$delivery.payments | json_encode}{/if}">
    
                                    {*if $delivery.logo?}
                                        <img src="{$delivery.logo}" alt="{$delivery.name}" class="esl-{$delivery.properties.service}" title="{$delivery.name}"/>
                                    {/if*}
    
                                    {$name}
    
                                    {if $delivery.description?}
                                        <p class="small">
                                            {$delivery.description}
                                        </p>
                                    {/if}
                                </label>
                                <div id="{$delivery.properties.service}-{$delivery.properties.mode}"></div>
                            </div>
                        {/foreach}
                    </div>
                </div> 
            </div>
        </div> 
    </div> 
    
    {*<button type="button" name="ms2_action" value="order/clean" class="mt-3 btn btn-danger ms2_link">
        {'ms2_frontend_order_cancel' | lexicon}
    </button>*}

    <hr>
    
    <div class="row esl-total-info">
        
        <div class="col-md-7">
            <p>В корзине: <span class="esl-cart-cost esl-loader"></span> {'ms2_frontend_currency' | lexicon}</p>
            <p class="esl-total-item">Стоимость доставки: <span class="esl-delivery-cost esl-loader"></span></p>
            <p class="esl-total-item">Способ доставки: <span class="esl-delivery-mode esl-loader"></span></p>
            <p class="esl-total-item">Адрес ПВЗ: <span class="esl-delivery-address"></span></p>
            <p>Итого, к оплате: <span class="esl_cost">{$order.cost ?: 0}</span> {'ms2_frontend_currency' | lexicon}</p>
        </div>

        <div class="col-md-5">
            <button type="submit" name="ms2_action" value="order/submit" class="btn btn-success btn-lg ms2_link orderSubmit"> {'ms2_frontend_order_submit' | lexicon}</button>
            <p class="small">
                Нажимая на кнопку «Оформить заказ»,<br> 
        	    вы даёте свое <a href="{242 | url}" target="_blank" title="согласие на обработку персональных данных">согласие на обработку персональных данных</a>
        	</p>
        </div>
    </div>
    
</form>