@php
    $forms = [
        [
            'fieldtype' => 'input',
            'label' => 'Firstname',
        ],
        [
            'fieldtype' => 'column break',
        ],
        [
            'fieldtype' => 'input',
            'label' => 'Lastname',
        ],
        [
            'fieldtype' => 'section break',
        ],
        [
            'fieldtype' => 'column break',
        ],
        [
            'fieldtype' => 'select',
            'label' => 'Gender',
            'options' => 'Male, Female',
        ],
        [
            'fieldtype' => 'column break',
        ],
        [
            'fieldtype' => 'numeric',
            'label' => 'Phone',
        ],
    ];
@endphp

<x-admin.layouts.form-customize title="{{ $title }}" :forms="$forms">

</x-admin.layouts.form-customize>
