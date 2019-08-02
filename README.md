# Widget File Uploader

This module adds the ability to add a file uploader field to widgets using only xml. 
Those files will be uploaded automatically. They will also be deleted automatically when they are 
overwritten from the admin panel to keep disk usage low.

## How to use

#### Add field to widget

When defining your widget, add a file uploader field like in the following example : 

```xml
<parameter name="file_upload" xsi:type="block" visible="true" required="false" sort_order="10">
    <label translate="true">File</label>
    <block class="Dcabrejas\WidgetFileUploader\Block\Adminhtml\FileUploader">
        <data>
            <item name="name" xsi:type="string">file_upload</item>
        </data>
    </block>
</parameter>
```

#### Configure form to accept file uploads

In your `etc/admin/di.xml` you can configure the widgets whose forms need to allow file uploads like this :

```xml
<virtualType name="formPlugin" type="Dcabrejas\WidgetFileUploader\Plugin\Widget\FormEnctypePlugin">
    <arguments>
        <argument name="widgetCodes" xsi:type="array">
            <item name="my_widget_code" xsi:type="string">my_widget_code</item>
        </argument>
    </arguments>
</virtualType>

<type name="Magento\Backend\Block\Widget\Form">
    <plugin name="Example_Module::set_widget_form_type" type="formPlugin" sortOrder="10"/>
</type>
```

#### Configure the file upload handler for each of your file upload fields

Also in your `etc/admin/di.xml` you can configure which widget fields are file uploads and 
need to be handled automatically. You can add as many as you want:

```xml
<!-- File Upload Handlers -->
    <virtualType name="uploadFieldHandler" type="Dcabrejas\WidgetFileUploader\Plugin\Widget\FileHandlerPlugin">
        <arguments>
            <argument name="fieldName" xsi:type="string">file_upload</argument>
            <argument name="widgetCode" xsi:type="string">my_widget_code</argument>
            <argument name="relativeStoragePath" xsi:type="string">/my/custom/path/</argument>
        </arguments>
    </virtualType>
    <virtualType name="uploadField2Handler" type="Dcabrejas\WidgetFileUploader\Plugin\Widget\FileHandlerPlugin">
        <arguments>
            <argument name="fieldName" xsi:type="string">file_upload2</argument>
            <argument name="widgetCode" xsi:type="string">my_widget_code</argument>
            <argument name="relativeStoragePath" xsi:type="string">/my/custom/path/</argument>
        </arguments>
    </virtualType>

    <type name="Magento\Widget\Model\Widget\Instance">
        <plugin name="Example_Module::field_upload_handler" type="uploadFieldHandler" sortOrder="10"/>
        <plugin name="Example_Module::field_upload2_handler" type="uploadField2Handler" sortOrder="20"/>
    </type>
```

## Installation

Add the following to your `repositories` key in your `composer.json` file:

```
{
	"type": "vcs",
	"url": "git@github.com:dcabrejas/m2-module-widget-file-uploader.git"
}        
```

Run `composer require dcabrejas/m2-module-widget-file-uploader`
