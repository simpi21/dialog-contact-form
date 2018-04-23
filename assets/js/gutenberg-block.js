(function (blocks, element, i18n) {
    'use strict';

    var el = element.createElement, // function to create elements
        SelectControl = wp.components.SelectControl, // select control
        InspectorControls = blocks.InspectorControls, // sidebar controls
        dcfGutenbergBlock = window.dcfGutenbergBlock,
        blockStyle = {backgroundColor: '#900', color: '#fff', padding: '20px'};

    blocks.registerBlockType('dialog-contact-form/form', {
        title: 'Dialog Contact Form',

        icon: 'feedback',

        category: 'common',

        attributes: {
            formID: {
                type: 'integer',
                default: 0
            }
        },

        edit: function (props) {
            var focus = props.focus;
            var formID = props.attributes.formID;
            var children = [];

            if (!formID) formID = ''; // Default.

            function onFormChange(newFormID) {
                // updates the form id on the props
                props.setAttributes({formID: newFormID});
            }

            // Set up the form dropdown in the side bar 'block' settings
            var inspectorControls = el(InspectorControls, {}, el(SelectControl, {
                label: i18n.__('Selected Form'),
                value: formID.toString(),
                options: dcfGutenbergBlock.forms,
                onChange: onFormChange
            }));

            /**
             * Create the div container, add an overlay so the user can interact
             * with the form in Gutenberg, then render the iframe with form
             */
            if ('' === formID) {
                children.push(
                    el('div', {style: {width: '100%'}},
                        el('h3', {className: 'dcf-forms-title'}, 'Dialog Contact Form'),
                        el(SelectControl, {
                            value: formID.toString(),
                            options: dcfGutenbergBlock.forms,
                            onChange: onFormChange
                        })
                    )
                );
            } else {
                children.push(
                    el('div', {className: 'dcf-form-container'},
                        el('div', {className: 'dcf-form-overlay'}),
                        el('iframe', {
                            src: dcfGutenbergBlock.siteUrl + '?dcf_forms_preview=1&dcf_forms_iframe&form_id=' + formID,
                            height: '0',
                            width: '500',
                            scrolling: 'no'
                        })
                    )
                )
            }

            return [children, !!focus && inspectorControls];
        },

        save: function (props) {
            var formID = props.attributes.formID;

            if (!formID)
                return '';
            /**
             * we're essentially just adding a short code, here is where
             * it's save in the editor
             *
             * return content wrapped in DIV as raw HTML is unsupported
             */
            var returnHTML = '[dialog_contact_form id=' + parseInt(formID) + ']';
            return el('div', null, returnHTML);
        },
    });

})(wp.blocks, wp.element, wp.i18n);