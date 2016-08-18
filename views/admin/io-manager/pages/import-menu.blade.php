<h3>Import WordPress Data</h3>

<p>Fill out the form below to begin importing a remote WordPress instance.</p>

<div class="row">
    <div class="col-sm-4">
        <ko-input params="
            label: 'Remote WordPress URL',
            value: importer.remoteUrl,
            placeholder: 'http://some-wordpress-site.com'
            "></ko-input>
    </div>
    <div class="col-sm-4">
        <ko-input params="
            label: 'Remote Security Key',
            value: importer.remoteSecurityKey,
            placeholder: Array(32).join('*')
            "></ko-input>
    </div>

</div>

<p data-bind="if: ! importer.canConnect()">
    <i>* A URL and valid security code from the remote must be provided before you can connect.</i>
</p>

<button class="btn btn-primary btn-sm btn-success" data-bind="
    disable: ! importer.canConnect()
">Connect to Remote</button>