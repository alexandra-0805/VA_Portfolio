<?php
/**
 * Definitions for the admin content editors (one generic page renders them all).
 *
 * Field options:
 *   label, type (text|textarea|url|number|icon|image), required, max (length),
 *   min/max for numbers, placeholder, col (Bootstrap column class), default.
 * `image` fields accept a path/URL and also show an upload input.
 */

return [
    'projects' => [
        'table' => 'projects',
        'title' => 'Projects',
        'singular' => 'project',
        'kicker' => 'Portfolio content',
        'order' => 'created_at DESC, id DESC',
        'upload_folder' => 'projects',
        'columns' => ['title' => 'Title', 'technologies' => 'Tech'],
        'fields' => [
            'title' => ['label' => 'Title', 'required' => true, 'max' => 150],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'required' => true, 'max' => 5000],
            'technologies' => ['label' => 'Technologies', 'max' => 255, 'placeholder' => 'HTML, CSS, Bootstrap'],
            'image' => ['label' => 'Image URL or path', 'type' => 'image', 'max' => 500],
            'project_url' => ['label' => 'Project URL', 'type' => 'url', 'max' => 500],
        ],
    ],

    'services' => [
        'table' => 'services',
        'title' => 'Services',
        'singular' => 'service',
        'kicker' => 'Portfolio content',
        'order' => 'id',
        'columns' => ['title' => 'Service', 'icon' => 'Icon'],
        'fields' => [
            'title' => ['label' => 'Service title', 'required' => true, 'max' => 150],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'required' => true, 'max' => 2000],
            'icon' => ['label' => 'Bootstrap icon class', 'type' => 'icon', 'required' => true, 'max' => 80, 'default' => 'bi-stars', 'placeholder' => 'bi-stars'],
        ],
    ],

    'skills' => [
        'table' => 'skills',
        'title' => 'Skills',
        'singular' => 'skill',
        'kicker' => 'Portfolio content',
        'order' => 'category, name',
        'columns' => ['name' => 'Name', 'category' => 'Category', 'proficiency' => 'Level'],
        'fields' => [
            'name' => ['label' => 'Skill name', 'required' => true, 'max' => 100],
            'category' => ['label' => 'Category', 'required' => true, 'max' => 100],
            'proficiency' => ['label' => 'Proficiency (%)', 'type' => 'number', 'required' => true, 'min' => 0, 'max' => 100, 'default' => 70],
        ],
    ],

    'certificates' => [
        'table' => 'certificates',
        'title' => 'Certificates',
        'singular' => 'certificate',
        'kicker' => 'Portfolio content',
        'order' => 'created_at DESC, id DESC',
        'upload_folder' => 'certificates',
        'columns' => ['title' => 'Title', 'issuer' => 'Issuer', 'date' => 'Date'],
        'fields' => [
            'title' => ['label' => 'Certificate title', 'required' => true, 'max' => 200],
            'issuer' => ['label' => 'Issuer', 'max' => 150, 'col' => 'col-sm-7'],
            'date' => ['label' => 'Date', 'max' => 80, 'col' => 'col-sm-5', 'placeholder' => 'February 2026'],
            'image' => ['label' => 'Image URL or path', 'type' => 'image', 'max' => 500],
            'url' => ['label' => 'Open link', 'type' => 'url', 'max' => 500, 'placeholder' => 'https://drive.google.com/...'],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 2000],
        ],
    ],
];
