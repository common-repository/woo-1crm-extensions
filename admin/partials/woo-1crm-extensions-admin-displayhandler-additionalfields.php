<?php

class displayhandler_additionalfields
{
    /**
     * Overview
     */
    public function overview()
    {
        ?>
        <div class="wrap">
            <h2>Woo 1CRM - Extensions</h2>

            <a href="<?php menu_page_url( 'woo1crm-additional-fields' ); ?>">Additional Fields</a>
        </div>
        <?php
    }

    /**
     * Additional Fields
     */
    public function additional_fields()
    {

        ?>
        <div class="wrap">
            <h2>Woo 1CRM - Additional Fields</h2>

            <form method="post" action="options.php" id="addfieldsform">
                <?php wp_nonce_field('additional_fields'); ?>
                <?php settings_fields('woo1crmadditionalfields-group'); ?>
                <?php do_settings_sections('woo1crmadditionalfields-group');

                $this->section_divs('billing');
                $this->section_divs('shipping');
                //$this->section_divs('account');
                $this->section_divs('order');
                ?>

                <?php submit_button(); ?>

            </form>
        </div>

        <?php
    }

    private function section_divs($prefix)
    {
        ?>
        <div id="<?php echo Woo_1crm_Extensions_Public_Setting::$SECTIONS[$prefix]['htmlid']; ?>div"
             class="customfieldsdivs">
            <h2><?= Woo_1crm_Extensions_Public_Setting::$SECTIONS[$prefix]['headlineD'] ?></h2>
            <input type="button" class="button action donotserialize newfield"
                   name="<?php echo Woo_1crm_Extensions_Public_Setting::$SECTIONS[$prefix]['htmlid']; ?>addButton"
                   id="<?php echo Woo_1crm_Extensions_Public_Setting::$SECTIONS[$prefix]['htmlid']; ?>addButton"
                   value="+ Feld hinzufügen">
            <?php $this->GenerateFieldsFromArrayString(get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS[$prefix]['htmlid'])) ?>
            <input id="<?php echo Woo_1crm_Extensions_Public_Setting::$SECTIONS[$prefix]['htmlid']; ?>" type="hidden"
                   name="<?php echo Woo_1crm_Extensions_Public_Setting::$SECTIONS[$prefix]['htmlid']; ?>"
                   value="<?php echo esc_attr(get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS[$prefix]['htmlid'])); ?>"
                   class="donotserialize"/>
        </div>

        <?php
    }

    private function GenerateFieldsFromArrayString($arraystring)
    {

        $decodedCustomfieldsArray = json_decode($arraystring, true);

        if ($decodedCustomfieldsArray !== null OR json_last_error() == JSON_ERROR_NONE) {

            $hidevalues = false;
            $valuestext = '';

            foreach ((array)$decodedCustomfieldsArray as $field) {
                ?>

                <?php
                if ($field['name'] == 'fieldname') {
                    ?>
                    <div>
                    <label for='fieldname'>Feld Name:<span
                                class='infoWindow'>Der im Bestellprozess angezeigte Feldname.</span></label>
                    <input id='fieldname' type='text' name='fieldname' value='<?= $field['value'] ?>' required><br/>
                    <?php

                } elseif ($field['name'] == 'fieldid') {
                    ?>
                    <label for='fieldid'>Feld-ID im 1CRM:<span class='infoWindow'>Zur fehlerfreien Synchronisation muss diese ID identisch mit der im 1CRM angegebenen Feld-ID sein. Keine Leerzeichen, Sonderzeichen und Umlaute verwenden!</span></label>
                    <input id='fieldid' type='text' name='fieldid' value='<?= $field['value'] ?>' required><br/>
                    <?php

                } elseif ($field['name'] == 'fieldtype') {
                    ?>
                    <label for='fieldtype'>Feld Typ:<span class='infoWindow'>Der Typ des im Bestellprozess angezeigten Feldes.</span></label>
                    <select id='fieldtype' class='fieldtype' name='fieldtype'><?php

                        if ($field['value'] == 'input') {
                            ?>
                            <option value='input' selected>input</option>
                            <option value='dropdown'>DropDown</option>
                            <option value='checkbox'>Checkbox</option>
                            <option value='textarea'>Textarea</option>
                            <?php
                            $hidevalues = true;

                        } elseif ($field['value'] == 'dropdown') {
                            ?>
                            <option value='input'>input</option>
                            <option value='dropdown' selected>DropDown</option>
                            <option value='checkbox'>Checkbox</option>
                            <option value='textarea'>Textarea</option>
                            <?php
                            $valuestext = "DropDown Inhalte (separiert durch Semikolon):";

                        } elseif ($field['value'] == 'checkbox') {
                            ?>
                            <option value='input'>input</option>
                            <option value='dropdown'>DropDown</option>
                            <option value='checkbox' selected>Checkbox</option>
                            <option value='textarea'>Textarea</option>
                            <?php
                            $hidevalues = true;

                        } elseif ($field['value'] == 'textarea') {
                            ?>
                            <option value='input'>input</option>
                            <option value='dropdown'>DropDown</option>
                            <option value='checkbox'>Checkbox</option>
                            <option value='textarea' selected>Textarea</option>
                            <?php
                            $hidevalues = true;
                        }
                        ?>
                    </select>
                    <?php

                } elseif ($field['name'] == 'fieldvalues') {
                    if ($hidevalues) {
                        ?><label for='fieldvalues' style='display: none;'>Feld Values:</label>
                        <input type='text'
                               id='fieldvalues'
                               name='fieldvalues'
                               value='<?= $field['value'] ?>'
                               style='display: none;'>
                        <?php
                        $hidevalues = false;

                    } else {
                        ?><label for='fieldvalues'><?= $valuestext ?><span class='infoWindow'>Bitte keine Sonderzeichen und Umlaute verwenden!</span>
                        </label>
                        <input id='fieldvalues' type='text' name='fieldvalues' value='<?= $field['value'] ?>' required>
                        <?php
                        $valuestext = '';
                    }
                    ?>
                    <input type='button' class='deleteButtons donotserialize button action'
                           value='- löschen'>
                    </div>
                    <?php
                }
                ?>
                <?php

            }

        }

    }
}