<?php
// upload
Breadcrumbs::for('upload', function ($trail) {
    $trail->push('Upload', route('upload'));
});


// Offline storage
Breadcrumbs::for('offline_storage', function ($trail) {
    $trail->push('Offline storage', route('offline_storage'));
});

// Offline storage > AIP List
Breadcrumbs::for('aip_list', function ($trail, $job) {
    $trail->parent('offline_storage');
    $trail->push('AIP List', route('offline_storage.show', $job->id));
});

// Offline storage > AIP List > Files
Breadcrumbs::for('file_list', function ($trail, $bag) {
    $job = $bag->job();
    if(isset($job))
        $trail->parent('aip_list', $job);
    else
        $trail->parent('upload');
    $trail->push('File List', route('bag.show.files', $bag->id));
});

// Offline storage > AIP List > Files > Metadata
Breadcrumbs::for('metadata_view', function ($trail, $file) {
    $trail->parent('file_list', $file->bag);
    $trail->push($file->filename, route('metadata.edit', ['bagId' => $file->bag->id, 'fileId' => $file->id]));
});

// Offline storage > Job Metadata
Breadcrumbs::for('offline_storage_metadata_view', function ($trail, $job) {
    $trail->parent('offline_storage');
    $trail->push($job->name, route('offline_storage.metadata.edit', ['id' => $job->id]));
});

// Offline storage > Content options
Breadcrumbs::for('content_options_view', function ($trail, $job) {
    $trail->parent('offline_storage');
    $trail->push('Content options '.$job->name, route('offline_storage.setup', ['id' => $job->id]));
});