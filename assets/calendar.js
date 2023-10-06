( function( blocks, blockEditor, element, components , serverSideRender ) {

    var el = element.createElement,
        registerBlockType = blocks.registerBlockType,
        ServerSideRender = serverSideRender,
        Fragment = element.Fragment,
        InspectorControls = blockEditor.InspectorControls,
        TextControl = components.TextControl,
        ToggleControl = components.ToggleControl,
        Panel = components.Panel,
        PanelBody = components.PanelBody,
        PanelRow = components.PanelRow;

    registerBlockType( 'brianhenryie/simple-calendar', {
        title: 'Simple Calendar',
        icon: 'calendar-alt',
        category: 'widgets',

        attributes: {
            calendarId: {
                type: 'string'
            },
            eventCount: {
                type: 'number',
                default: 10
            },
            eventPeriod: {
                type: 'number',
                default: 92
            },
            dateFormat: {
                'type': 'string',
                'default': 'l jS \\of F'
            }
        },

        edit: function( props ) {

            return (
                el( Fragment, {},
                    el( InspectorControls, {},
                        el( PanelBody, { title: 'Calendar Settings', initialOpen: true },
                            /* Text Field */

                            el( PanelRow, {},
                                el( TextControl,
                                    {
                                        label: 'Calendar ID, or iCal URL:',
                                        onChange: ( value ) => {
                                            props.setAttributes( { calendarId: value } );
                                        },
                                        value: props.attributes.calendarId
                                    }
                                )
                            ),
                            el( PanelRow, {},
                                el( TextControl,
                                    {
                                        label: 'Maximum number of events displayed:',
                                        onChange: ( value ) => {
                                            props.setAttributes( { eventCount: parseInt( value ) } );
                                        },
                                        value: props.attributes.eventCount
                                    }
                                )
                            ),
                            el( PanelRow, {},
                                el( TextControl,
                                    {
                                        label: 'Maximum number of days after today with events displayed:',
                                        onChange: ( value ) => {
                                            props.setAttributes( { eventPeriod: parseInt( value ) } );
                                        },
                                        value: props.attributes.eventPeriod
                                    }
                                )
                            ),
                            el( PanelRow, {},
                                el( TextControl,
                                    {
                                        label: 'Date format:',
                                        onChange: ( value ) => {
                                            props.setAttributes( { dateFormat: value } );
                                        },
                                        value: props.attributes.dateFormat
                                    }
                                )
                            ),


                        ),

                    ),

                    /*
                     * Here will be your block markup
                     */

                    el( ServerSideRender, { block:"brianhenryie/simple-calendar", attributes: props.attributes } )
                )
            );
        },
        save: function ( props ) {
            return null;
        },
    } );
}(
    window.wp.blocks,
    window.wp.blockEditor,
    window.wp.element,
    window.wp.components,
    window.wp.serverSideRender,
) );