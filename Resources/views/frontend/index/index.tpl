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
{/block}