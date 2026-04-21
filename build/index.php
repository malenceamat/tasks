<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="contact-form">
    <?if ($arResult["FORM_NOTE"]):?>
        <div style="color: green; padding: 25px; margin-bottom: 20px; border: 2px solid green; font-size: 18px; text-align: center;">
            <strong>Спасибо! Ваша заявка успешно отправлена.</strong><br><br>
            Наши сотрудники свяжутся с вами в ближайшее время.
        </div>
    <?endif;?>
    <?if (!$arResult["FORM_NOTE"]):?>
        <?=$arResult["FORM_HEADER"]?>
        <div class="contact-form__head">
            <div class="contact-form__head-title">Связаться</div>
            <div class="contact-form__head-text">
                Наши сотрудники помогут выполнить подбор услуги и расчет цены
            </div>
        </div>
        <div class="contact-form__form">
            <div class="contact-form__form-inputs">
                <?foreach ($arResult["QUESTIONS"] as $FIELD):
                    $q = $FIELD["STRUCTURE"][0];
                    ?>
                    <div class="input contact-form__input">
                        <label class="input__label">
                            <div class="input__label-text">
                                <?=$FIELD["CAPTION"]?>
                                <?if($FIELD["REQUIRED"] == "Y"):?> *<?endif;?>
                            </div>
                            <?if($q["FIELD_TYPE"] == "textarea"):?>
                                <textarea
                                        name="form_textarea_<?=$q["ID"]?>"
                                        class="input__input"
                                    <?=$FIELD["REQUIRED"] == "Y" ? 'required' : ''?>></textarea>
                            <?else:?>
                                <input
                                        type="<?= $q["FIELD_TYPE"] == "email" ? "email" : "text" ?>"
                                        name="form_<?=$q["FIELD_TYPE"]?>_<?=$q["ID"]?>"
                                        class="input__input"
                                    <?=$FIELD["REQUIRED"] == "Y" ? 'required' : ''?>>
                            <?endif;?>
                        </label>
                    </div>
                <?endforeach;?>
            </div>
            <div class="contact-form__bottom">
                <div class="contact-form__bottom-policy">
                    Нажимая «Отправить», вы соглашаетесь на обработку данных
                </div>
                <button type="submit" name="web_form_submit" value="Y" class="form-button contact-form__bottom-button">
                    <div class="form-button__title">Оставить заявку</div>
                </button>
            </div>
        </div>
        <?=$arResult["FORM_FOOTER"]?>
    <?endif;?>
</div>