{{-- Display Property Border Start --}}
<div id="display_property_Borders_panel" class="position-relative w-25 d-none ml-1">
    <div class="d-flex align-items-center bg-warning p-1">
        <div class="mr-auto"><span class="fa fa-wrench"></span> Borders Setting</div>
        <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_Borders" role="button">
            <i class="fa fa-toggle-down"></i>
        </div>
        <div class="showhide" data-target="#display_property_Borders_panel">
            <i class="fa fa-window-close-o"></i>
        </div>
    </div>
    <div id="display_property_Borders" class="collapse border container bg-light position-absolute w-100" style="right:0">
        <form class="display_property_form pb-2" property-name="Borders">
            <div class="form-group">
                <label>Additive</label>
                <select multiple class="form-control" name="name">
                    <option>border</option>
                    <option>border-top</option>
                    <option>border-right</option>
                    <option>border-bottom</option>
                    <option>border-left</option>
                </select>
            </div>
            <div class="form-group">
                <label>Subtractive</label>
                <select multiple class="form-control" name="name">
                    <option>border-0</option>
                    <option>border-top-0</option>
                    <option>border-right-0</option>
                    <option>border-bottom-0</option>
                    <option>border-left-0</option>
                </select>
            </div>
            <div class="form-group">
                <label>Border color</label>
                <select multiple class="form-control" name="name">
                    <option>border-primary</option>
                    <option>border-secondary</option>
                    <option>border-success</option>
                    <option>border-danger</option>
                    <option>border-warning</option>
                    <option>border-info</option>
                    <option>border-light</option>
                    <option>border-dark</option>
                    <option>border-white</option>
                </select>
            </div>
            <div class="form-group">
                <label>Border radius</label>
                <select multiple class="form-control" name="name">
                    <option>rounded</option>
                    <option>rounded-top</option>
                    <option>rounded-right</option>
                    <option>rounded-bottom</option>
                    <option>rounded-left</option>
                    <option>rounded-circle</option>
                    <option>rounded-0</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
{{-- Display Property Border END --}}
{{-- Display Property Colors START --}}
<div id="display_property_Colors_panel" class="position-relative w-25 d-none ml-1">
    <div class="d-flex align-items-center bg-warning p-1">
        <div class="mr-auto"><span class="fa fa-wrench"></span> Colors Setting</div>
        <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_Colors" role="button">
            <i class="fa fa-toggle-down"></i>
        </div>
        <div class="showhide" data-target="#display_property_Colors_panel">
            <i class="fa fa-window-close-o"></i>
        </div>
    </div>
    <div id="display_property_Colors" class="collapse border container bg-light position-absolute w-100" style="right:0">
        <form class="display_property_form pb-2" property-name="Colors">
            <div class="form-group">
                <label>Color</label>
                <select multiple class="form-control" name="name">
                    <option>text-primary</option>
                    <option>text-secondary</option>
                    <option>text-success</option>
                    <option>text-danger</option>
                    <option>text-warning</option>
                    <option>text-info</option>
                    <option>text-light</option>
                    <option>text-dark</option>
                    <option>text-muted</option>
                    <option>text-white</option>
                </select>
            </div>
            <div class="form-group">
                <label>Background color</label>
                <select multiple class="form-control" name="name">
                    <option>bg-primary</option>
                    <option>bg-secondary</option>
                    <option>bg-success</option>
                    <option>bg-danger</option>
                    <option>bg-warning</option>
                    <option>bg-info</option>
                    <option>bg-light</option>
                    <option>bg-dark</option>
                    <option>bg-white</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
{{-- Display Property Colors End --}}
{{-- Display Property Display START --}}
<div id="display_property_Display_panel" class="position-relative w-25 d-none ml-1">
    <div class="d-flex align-items-center bg-warning p-1">
        <div class="mr-auto"><span class="fa fa-wrench"></span> Display Setting</div>
        <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_Display" role="button">
            <i class="fa fa-toggle-down"></i>
        </div>
        <div class="showhide" data-target="#display_property_Display_panel">
            <i class="fa fa-window-close-o"></i>
        </div>
    </div>
    <div id="display_property_Display" class="collapse border container bg-light position-absolute w-100" style="right:0">
        <form class="display_property_form pb-2" property-name="Display">
            <div class="form-row">
                <div class="form-group col-md-6">
                <label>Screen</label>
                <select class="form-control" id="Display_screen">
                    <option>xs</option>
                    <option>sm</option>
                    <option>md</option>
                    <option>lg</option>
                    <option>xl</option>
                </select>
                </div>
                <div class="form-group col-md-6">
                <label>Value</label>
                <select class="form-control" id="Display_value">
                    <option>none</option>
                    <option>inline</option>
                    <option>inline-block</option>
                    <option>block</option>
                    <option>table</option>
                    <option>table-cell</option>
                    <option>table-row</option>
                    <option>flex</option>
                    <option>inline-flex</option>
                </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
{{-- Display Property Display END --}}
{{-- Display Property Flex START --}}
<div id="display_property_Flex_panel" class="position-relative w-25 d-none ml-1">
    <div class="d-flex align-items-center bg-warning p-1">
        <div class="mr-auto"><span class="fa fa-wrench"></span> Flex Setting</div>
        <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_Flex" role="button">
            <i class="fa fa-toggle-down"></i>
        </div>
        <div class="showhide" data-target="#display_property_Flex_panel">
            <i class="fa fa-window-close-o"></i>
        </div>
    </div>
    <div id="display_property_Flex" class="collapse border container bg-light position-absolute w-100" style="right:0">
        <form class="display_property_form pb-2" property-name="Flex">
            <h5>Flex Container</h5>
            <div class="form-group">
                <label>Direction</label>
                <select multiple class="form-control" name="name">
                    <option>flex-row</option>
                    <option>flex-row-reverse</option>
                    <option>flex-column-reverse</option>
                    <option>flex-column</option>
                    <option>flex-sm-row</option>
                    <option>flex-sm-row-reverse</option>
                    <option>flex-sm-column</option>
                    <option>flex-sm-column-reverse</option>
                    <option>flex-md-row</option>
                    <option>flex-md-row-reverse</option>
                    <option>flex-md-column</option>
                    <option>flex-md-column-reverse</option>
                    <option>flex-lg-row</option>
                    <option>flex-lg-row-reverse</option>
                    <option>flex-lg-column</option>
                    <option>flex-lg-column-reverse</option>
                    <option>flex-xl-row</option>
                    <option>flex-xl-row-reverse</option>
                    <option>flex-xl-column</option>
                    <option>flex-xl-column-reverse</option>
                </select>
            </div>
            <div class="form-group">
                <label>Justify content</label>
                <select multiple class="form-control" name="name">
                    <option>justify-content-start</option>
                    <option>justify-content-end</option>
                    <option>justify-content-center</option>
                    <option>justify-content-between</option>
                    <option>justify-content-around</option>
                    <option>justify-content-sm-start</option>
                    <option>justify-content-sm-end</option>
                    <option>justify-content-sm-center</option>
                    <option>justify-content-sm-between</option>
                    <option>justify-content-sm-around</option>
                    <option>justify-content-md-start</option>
                    <option>justify-content-md-end</option>
                    <option>justify-content-md-center</option>
                    <option>justify-content-md-between</option>
                    <option>justify-content-md-around</option>
                    <option>justify-content-lg-start</option>
                    <option>justify-content-lg-end</option>
                    <option>justify-content-lg-center</option>
                    <option>justify-content-lg-between</option>
                    <option>justify-content-lg-around</option>
                    <option>justify-content-xl-start</option>
                    <option>justify-content-xl-end</option>
                    <option>justify-content-xl-center</option>
                    <option>justify-content-xl-between</option>
                    <option>justify-content-xl-around</option>
                </select>
            </div>
            <div class="form-group">
                <label>Align items</label>
                <select multiple class="form-control" name="name">
                    <option>align-items-start</option>
                    <option>align-items-end</option>
                    <option>align-items-center</option>
                    <option>align-items-baseline</option>
                    <option>align-items-stretch</option>
                    <option>align-items-sm-start</option>
                    <option>align-items-sm-end</option>
                    <option>align-items-sm-center</option>
                    <option>align-items-sm-baseline</option>
                    <option>align-items-sm-stretch</option>
                    <option>align-items-md-start</option>
                    <option>align-items-md-end</option>
                    <option>align-items-md-center</option>
                    <option>align-items-md-baseline</option>
                    <option>align-items-md-stretch</option>
                    <option>align-items-lg-start</option>
                    <option>align-items-lg-end</option>
                    <option>align-items-lg-center</option>
                    <option>align-items-lg-baseline</option>
                    <option>align-items-lg-stretch</option>
                    <option>align-items-xl-start</option>
                    <option>align-items-xl-end</option>
                    <option>align-items-xl-center</option>
                    <option>align-items-xl-baseline</option>
                    <option>align-items-xl-stretch</option>
                </select>
            </div>
            <div class="form-group">
                <label>Wrap</label>
                <select multiple class="form-control" name="name">
                    <option>flex-nowrap</option>
                    <option>flex-wrap</option>
                    <option>flex-wrap-reverse</option>
                    <option>flex-sm-nowrap</option>
                    <option>flex-sm-wrap</option>
                    <option>flex-sm-wrap-reverse</option>
                    <option>flex-md-nowrap</option>
                    <option>flex-md-wrap</option>
                    <option>flex-md-wrap-reverse</option>
                    <option>flex-lg-nowrap</option>
                    <option>flex-lg-wrap</option>
                    <option>flex-lg-wrap-reverse</option>
                    <option>flex-xl-nowrap</option>
                    <option>flex-xl-wrap</option>
                    <option>flex-xl-wrap-reverse</option>
                </select>
            </div>
            <div class="form-group">
                <label>Align content</label>
                <select multiple class="form-control" name="name">
                    <option>align-content-start</option>
                    <option>align-content-end</option>
                    <option>align-content-center</option>
                    <option>align-content-around</option>
                    <option>align-content-stretch</option>
                    <option>align-content-sm-start</option>
                    <option>align-content-sm-end</option>
                    <option>align-content-sm-center</option>
                    <option>align-content-sm-around</option>
                    <option>align-content-sm-stretch</option>
                    <option>align-content-md-start</option>
                    <option>align-content-md-end</option>
                    <option>align-content-md-center</option>
                    <option>align-content-md-around</option>
                    <option>align-content-md-stretch</option>
                    <option>align-content-lg-start</option>
                    <option>align-content-lg-end</option>
                    <option>align-content-lg-center</option>
                    <option>align-content-lg-baseline</option>
                    <option>align-content-lg-stretch</option>
                    <option>align-content-xl-start</option>
                    <option>align-content-xl-end</option>
                    <option>align-content-xl-center</option>
                    <option>align-content-xl-baseline</option>
                    <option>align-content-xl-stretch</option>
                </select>
            </div>
            <h5>Flex Items</h5>
            <div class="form-group">
                <label>Align self</label>
                <select multiple class="form-control" name="name">
                    <option>align-self-start</option>
                    <option>align-self-end</option>
                    <option>align-self-center</option>
                    <option>align-self-baseline</option>
                    <option>align-self-stretch</option>
                    <option>align-self-sm-start</option>
                    <option>align-self-sm-end</option>
                    <option>align-self-sm-center</option>
                    <option>align-self-sm-baseline</option>
                    <option>align-self-sm-stretch</option>
                    <option>align-self-md-start</option>
                    <option>align-self-md-end</option>
                    <option>align-self-md-center</option>
                    <option>align-self-md-baseline</option>
                    <option>align-self-md-stretch</option>
                    <option>align-self-lg-start</option>
                    <option>align-self-lg-end</option>
                    <option>align-self-lg-center</option>
                    <option>align-self-lg-baseline</option>
                    <option>align-self-lg-stretch</option>
                    <option>align-self-xl-start</option>
                    <option>align-self-xl-end</option>
                    <option>align-self-xl-center</option>
                    <option>align-self-xl-baseline</option>
                    <option>align-self-xl-stretch</option>
                </select>
            </div>
            <div class="form-group">
                <label>Order</label>
                <select multiple class="form-control" name="name">
                    <option>order-0</option>
                    <option>order-1</option>
                    <option>order-2</option>
                    <option>order-3</option>
                    <option>order-4</option>
                    <option>order-5</option>
                    <option>order-6</option>
                    <option>order-7</option>
                    <option>order-8</option>
                    <option>order-9</option>
                    <option>order-10</option>
                    <option>order-11</option>
                    <option>order-12</option>
                    <option>order-sm-0</option>
                    <option>order-sm-1</option>
                    <option>order-sm-2</option>
                    <option>order-sm-3</option>
                    <option>order-sm-4</option>
                    <option>order-sm-5</option>
                    <option>order-sm-6</option>
                    <option>order-sm-7</option>
                    <option>order-sm-8</option>
                    <option>order-sm-9</option>
                    <option>order-sm-10</option>
                    <option>order-sm-11</option>
                    <option>order-sm-12</option>
                    <option>order-md-0</option>
                    <option>order-md-1</option>
                    <option>order-md-2</option>
                    <option>order-md-3</option>
                    <option>order-md-4</option>
                    <option>order-md-5</option>
                    <option>order-md-6</option>
                    <option>order-md-7</option>
                    <option>order-md-8</option>
                    <option>order-md-9</option>
                    <option>order-md-10</option>
                    <option>order-md-11</option>
                    <option>order-md-12</option>
                    <option>order-lg-0</option>
                    <option>order-lg-1</option>
                    <option>order-lg-2</option>
                    <option>order-lg-3</option>
                    <option>order-lg-4</option>
                    <option>order-lg-5</option>
                    <option>order-lg-6</option>
                    <option>order-lg-7</option>
                    <option>order-lg-8</option>
                    <option>order-lg-9</option>
                    <option>order-lg-10</option>
                    <option>order-lg-11</option>
                    <option>order-lg-12</option>
                    <option>order-xl-0</option>
                    <option>order-xl-1</option>
                    <option>order-xl-2</option>
                    <option>order-xl-3</option>
                    <option>order-xl-4</option>
                    <option>order-xl-5</option>
                    <option>order-xl-6</option>
                    <option>order-xl-7</option>
                    <option>order-xl-8</option>
                    <option>order-xl-9</option>
                    <option>order-xl-10</option>
                    <option>order-xl-11</option>
                    <option>order-xl-12</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
{{-- Display Property Flex END --}}
{{-- Display Property Float START --}}
<div id="display_property_Float_panel" class="position-relative w-25 d-none ml-1">
    <div class="d-flex align-items-center bg-warning p-1">
        <div class="mr-auto"><span class="fa fa-wrench"></span> Float Setting</div>
        <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_Float" role="button">
            <i class="fa fa-toggle-down"></i>
        </div>
        <div class="showhide" data-target="#display_property_Float_panel">
            <i class="fa fa-window-close-o"></i>
        </div>
    </div>
    <div id="display_property_Float" class="collapse border container bg-light position-absolute w-100" style="right:0">
            <form class="display_property_form pb-2" property-name="Float">
                <div class="form-group">
                    <label>Float</label>
                    <select multiple class="form-control" name="name">
                        <option>float-left</option>
                        <option>float-right</option>
                        <option>float-none</option>
                        <option>float-sm-left</option>
                        <option>float-sm-right</option>
                        <option>float-sm-none</option>
                        <option>float-md-left</option>
                        <option>float-md-right</option>
                        <option>float-md-none</option>
                        <option>float-lg-left</option>
                        <option>float-lg-right</option>
                        <option>float-lg-none</option>
                        <option>float-xl-left</option>
                        <option>float-xl-right</option>
                        <option>float-xl-none</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
    </div>
</div>
{{-- Display Property Float END --}}
{{-- Display Property Position START --}}
<div id="display_property_Position_panel" class="position-relative w-25 d-none ml-1">
    <div class="d-flex align-items-center bg-warning p-1">
        <div class="mr-auto"><span class="fa fa-wrench"></span> Position Setting</div>
        <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_Position" role="button">
            <i class="fa fa-toggle-down"></i>
        </div>
        <div class="showhide" data-target="#display_property_Position_panel">
            <i class="fa fa-window-close-o"></i>
        </div>
    </div>
    <div id="display_property_Position" class="collapse border container bg-light position-absolute w-100" style="right:0">
            <form class="display_property_form pb-2" property-name="Position">
                <div class="form-group">
                    <label>Position</label>
                    <select class="form-control" name="name">
                        <option>position-static</option>
                        <option>position-relative</option>
                        <option>position-absolute</option>
                        <option>position-fixed</option>
                        <option>position-sticky</option>
                        <option>fixed-top</option>
                        <option>fixed-bottom</option>
                        <option>sticky-top</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
    </div>
</div>
{{-- Display Property Position END --}}
{{-- Display Property Sizing START --}}
<div id="display_property_Sizing_panel" class="position-relative w-25 d-none ml-1">
    <div class="d-flex align-items-center bg-warning p-1">
        <div class="mr-auto"><span class="fa fa-wrench"></span> Sizing Setting</div>
        <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_Sizing" role="button">
            <i class="fa fa-toggle-down"></i>
        </div>
        <div class="showhide" data-target="#display_property_Sizing_panel">
            <i class="fa fa-window-close-o"></i>
        </div>
    </div>
    <div id="display_property_Sizing" class="collapse border container bg-light position-absolute w-100" style="right:0">
            <form class="display_property_form pb-2" property-name="Sizing">
                <div class="form-group">
                    <label>Layout</label>
                    <select multiple class="form-control" name="name">
                        <option>container</option>
                        <option>container-fluid</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Width</label>
                    <select multiple class="form-control" name="name">
                        <option>mw-100</option>
                        <option>w-25</option>
                        <option>w-50</option>
                        <option>w-75</option>
                        <option>w-100</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Height</label>
                    <select multiple class="form-control" name="name">
                        <option>mh-100</option>
                        <option>h-25</option>
                        <option>h-50</option>
                        <option>h-75</option>
                        <option>h-100</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
    </div>
</div>
{{-- Display Property Sizing END --}}
{{-- Display Property Spacing START --}}
<div id="display_property_Spacing_panel" class="position-relative w-25 d-none ml-1">
    <div class="d-flex align-items-center bg-warning p-1">
        <div class="mr-auto"><span class="fa fa-wrench"></span> Spacing Setting</div>
        <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_Spacing" role="button">
            <i class="fa fa-toggle-down"></i>
        </div>
        <div class="showhide" data-target="#display_property_Spacing_panel">
            <i class="fa fa-window-close-o"></i>
        </div>
    </div>
    <div id="display_property_Spacing" class="collapse border container bg-light position-absolute w-100" style="right:0">
            <form class="display_property_form pb-2" property-name="Spacing">
                <div class="form-row">
                    <div class="form-group col-md-3">
                    <label>Screen</label>
                    <select class="form-control" id="Spacing_screen">
                        <option>xs</option>
                        <option>sm</option>
                        <option>md</option>
                        <option>lg</option>
                        <option>xl</option>
                    </select>
                    </div>
                    <div class="form-group col-md-3">
                    <label>Property</label>
                    <select class="form-control" id="Spacing_property">
                        <option value="m">Margin</option>
                        <option value="p">Padding</option>
                    </select>
                    </div>
                    <div class="form-group col-md-3">
                    <label>Side</label>
                    <select class="form-control" id="Spacing_sides">
                        <option value="">All Side</option>
                        <option value="t">Top</option>
                        <option value="b">Bottom</option>
                        <option value="l">Left</option>
                        <option value="r">Right</option>
                        <option value="x">Left Right</option>
                        <option value="y">Top Bottom</option>
                    </select>
                    </div>
                    <div class="form-group col-md-3">
                    <label>Size</label>
                    <select class="form-control" id="Spacing_size">
                        <option>auto</option>
                        <option>0</option>
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
    </div>
</div>
{{-- Display Property Spacing END --}}
{{-- Display Property Text START --}}
<div id="display_property_Text_panel" class="position-relative w-25 d-none ml-1">
    <div class="d-flex align-items-center bg-warning p-1">
        <div class="mr-auto"><span class="fa fa-wrench"></span> Text Setting</div>
        <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_Text" role="button">
            <i class="fa fa-toggle-down"></i>
        </div>
        <div class="showhide" data-target="#display_property_Text_panel">
            <i class="fa fa-window-close-o"></i>
        </div>
    </div>
    <div id="display_property_Text" class="collapse border container bg-light position-absolute w-100" style="right:0">
            <form class="display_property_form pb-2" property-name="Text">
                <div class="form-group">
                    <label>Text</label>
                    <select multiple class="form-control" name="name">
                        <option>text-nowrap</option>
                        <option>text-truncate</option>
                        <option>text-lowercase</option>
                        <option>text-uppercase</option>
                        <option>text-capitalize</option>
                        <option>font-weight-bold</option>
                        <option>font-weight-light</option>
                        <option>font-italic</option>
                        <option>text-justify</option>
                        <option>text-left</option>
                        <option>text-center</option>
                        <option>text-right</option>
                        <option>text-sm-left</option>
                        <option>text-sm-center</option>
                        <option>text-sm-right</option>
                        <option>text-md-left</option>
                        <option>text-md-center</option>
                        <option>text-md-right</option>
                        <option>text-lg-left</option>
                        <option>text-lg-center</option>
                        <option>text-lg-right</option>
                        <option>text-xl-left</option>
                        <option>text-xl-center</option>
                        <option>text-xl-right</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
    </div>
</div>
{{-- Display Property Text END --}}
{{-- Display Property VerticalAlign START --}}
<div id="display_property_VerticalAlign_panel" class="position-relative w-25 d-none ml-1">
    <div class="d-flex align-items-center bg-warning p-1">
        <div class="mr-auto"><span class="fa fa-wrench"></span> VerticalAlign Setting</div>
        <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_VerticalAlign" role="button">
            <i class="fa fa-toggle-down"></i>
        </div>
        <div class="showhide" data-target="#display_property_VerticalAlign_panel">
            <i class="fa fa-window-close-o"></i>
        </div>
    </div>
    <div id="display_property_VerticalAlign" class="collapse border container bg-light position-absolute w-100" style="right:0">
            <form class="display_property_form pb-2" property-name="VerticalAlign">
                <div class="form-group">
                    <label>inline, inline-block, inline-table, and table cell elements</label>
                    <select multiple class="form-control" name="name">
                        <option>align-baseline</option>
                        <option>align-top</option>
                        <option>align-middle</option>
                        <option>align-bottom</option>
                        <option>align-text-bottom</option>
                        <option>align-text-top</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
    </div>
</div>
{{-- Display Property VerticalAlign END --}}