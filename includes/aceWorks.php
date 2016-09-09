<div class="col-md-12" id="view">
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading"><h1>ACE Works</h1></div>
            <div class="panel-body">
                <pre id="myCodeEditor"></pre>
            </div>
        </div>

        <!-- Standard button -->
        <button type="button" class="btn btn-default">Default</button>

        <!-- Provides extra visual weight and identifies the primary action in a set of buttons -->
        <button type="button" class="btn btn-primary">Primary</button>

        <!-- Indicates a successful or positive action -->
        <button type="button" class="btn btn-success">Success</button>

        <!-- Contextual button for informational alert messages -->
        <button type="button" class="btn btn-info">Info</button>

        <!-- Indicates caution should be taken with this action -->
        <button type="button" class="btn btn-warning">Warning</button>

        <!-- Indicates a dangerous or potentially negative action -->
        <button type="button" class="btn btn-danger">Danger</button>

        <!-- Deemphasize a button by making it look like a link while maintaining button behavior -->
        <button type="button" class="btn btn-link">Link</button>
    </div>
</div>

<style type="text/css">
    #myCodeEditor {
        margin: 0;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 500px;
    }
</style>

<script src="https://cloud9ide.github.io/emmet-core/emmet.js"></script>
<script type="text/javascript">

    var editor = ace.edit("myCodeEditor");
    editor.session.setMode("ace/mode/html");
    editor.setTheme("ace/theme/monokai");
    // Habilita o emmet para o editor
    editor.setOption("enableEmmet", true);
    // Desabilita mensagem no js:
    // Automatically scrolling cursor into view after selection change this will be disabled in the next version set editor.$blockScrolling = Infinity to disable this message
    editor.$blockScrolling = Infinity;
</script>