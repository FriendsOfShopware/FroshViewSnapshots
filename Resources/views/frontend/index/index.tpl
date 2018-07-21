{extends file='parent:frontend/index/index.tpl'}

{block name="frontend_index_header_javascript"}
    {$smarty.block.parent}
    <script type="text/javascript">
        window.snapshots = {
            'config': {
                    'sessionId': '{$snapshotSessionID}'
                {if !$snapshotStep}
                    , 'startUrl': '{url controller="snapshots" action="startRecording"}'
                    , 'stopUrl': '{url controller="snapshots" action="stopRecording"}'
                {else}
                    {if $snapshotNextStep}
                        , 'nextUrl': '{url controller="snapshots" action="load" session=$snapshotSessionID step=$snapshotNextStep}'
                    {/if}
                    {if $snapshotPrevStep}
                        , 'prevUrl': '{url controller="snapshots" action="load" session=$snapshotSessionID step=$snapshotPrevStep}'
                    {/if}
                    , 'currentStep': {$snapshotStep}
                {/if}
                , 'isRecordingSnapshots': {if $isSessionRecorded}true{else}false{/if}
            }
        }
    </script>
    <script src="{link file='custom/plugins/FroshViewSnapshots/Resources/views/frontend/_public/src/js/view-snapshots.js'}" type="text/javascript"></script>
    <style>
        .recorder {
            position: fixed;
            bottom: 0;
            right: 0;
            background: white;
            z-index: 99999;
            display: flex;
            box-shadow: 0 0 4px 1px #dadae5;
        }

        .recorder span {
            padding: 0 15px;
            line-height: 38px;
        }

        .recorder+.btn {
            margin-left: 15px;
        }
    </style>
    <div class="recorder">
        {if $snapshotStep}
            {if $snapshotPrevStep}
                <a onclick="snapshots.prev()" class="btn is--primary">
                    <i class='icon--previous'></i> Previous
                </a>
            {/if}
            <span>Current step: {$snapshotStep}</span>
            {if $snapshotNextStep}
                <a onclick="snapshots.next()" class="btn is--primary">
                    Next <i class='icon--next'></i>
                </a>
            {/if}
        {else}
            {if $isSessionRecorded}
                <span id="recorder-state">Recording!</span>
                <a id="recorder-button" onclick="snapshots.stop()" class="btn is--primary">
                    <i class='icon--stop'></i> Stop Recording
                </a>
            {else}
                <span id="recorder-state">Currently not recording...</span>
                <a id="recorder-button" onclick="snapshots.record()" class="btn is--primary">
                    <i class='icon--record'></i> Start Recording    
                </a>
            {/if}
        {/if}
    </div>
{/block}