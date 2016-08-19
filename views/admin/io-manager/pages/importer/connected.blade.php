<!-- ko if: 'connected' == importer.screen() -->

<h4 style="color: green">
    Connected to Remote
    <i class="fa fa-check"></i>
</h4>

<p>
    Session ID:
    <small data-bind="text: importer.session.sessionId() ? importer.session.sessionId() : '...'"></small>
</p>

@include('admin.io-manager.pages.importer.connected.remote-wp-instance')
@include('admin.io-manager.pages.importer.connected.import-form')
<!-- /ko -->