<?php
$files = [
    'resources/views/components/layouts/admin.blade.php',
    'resources/views/components/layouts/guest.blade.php',
    'resources/views/components/layouts/ppdb-form.blade.php'
];

$replacements = [
    'bg-blue-600' => 'bg-emerald-600',
    'text-blue-600' => 'text-emerald-600',
    'border-blue-' => 'border-emerald-',
    'from-blue-' => 'from-emerald-',
    'via-indigo-' => 'via-teal-',
    'to-purple-' => 'to-emerald-',
    'shadow-blue-' => 'shadow-emerald-',
    'text-gradient-blue' => 'text-gradient-emerald',
    'bg-blue-50' => 'bg-emerald-50',
    'bg-blue-100' => 'bg-emerald-100',
    'text-blue-100' => 'text-emerald-100',
    'text-blue-400' => 'text-emerald-400',
    'text-blue-500' => 'text-emerald-500',
    'bg-blue-500' => 'bg-emerald-500',
    'bg-indigo-' => 'bg-teal-',
    'text-indigo-' => 'text-teal-',
    'from-indigo-' => 'from-teal-',
    'shadow-indigo-' => 'shadow-teal-',
    'text-purple-' => 'text-emerald-',
    'bg-purple-' => 'bg-emerald-',
    'from-purple-' => 'from-emerald-',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($file, $content);
        echo "Processed $file\n";
    }
}
echo "Done\n";
