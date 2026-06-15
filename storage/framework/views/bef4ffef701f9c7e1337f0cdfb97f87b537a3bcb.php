<div class="flex h-screen antialiased text-gray-800">
    <div class="flex flex-row h-full w-full overflow-x-hidden">

        <div class="w-full md:w-1/3 flex-shrink-0 border-r border-gray-200 <?php echo e($selectedConversation ? 'hidden md:flex' : 'flex'); ?>">
            <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('conversation-list', ['selectedConvId' => $selectedConversation['id'] ?? null])->html();
} elseif ($_instance->childHasBeenRendered('l3001524830-0')) {
    $componentId = $_instance->getRenderedChildComponentId('l3001524830-0');
    $componentTag = $_instance->getRenderedChildComponentTagName('l3001524830-0');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l3001524830-0');
} else {
    $response = \Livewire\Livewire::mount('conversation-list', ['selectedConvId' => $selectedConversation['id'] ?? null]);
    $html = $response->html();
    $_instance->logRenderedChild('l3001524830-0', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
        </div>

        <div class="flex-auto h-full ">
            <?php if($selectedConversation): ?>
                <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('chat-box', ['conversationData' => $selectedConversation])->html();
} elseif ($_instance->childHasBeenRendered($selectedConversation['id'])) {
    $componentId = $_instance->getRenderedChildComponentId($selectedConversation['id']);
    $componentTag = $_instance->getRenderedChildComponentTagName($selectedConversation['id']);
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild($selectedConversation['id']);
} else {
    $response = \Livewire\Livewire::mount('chat-box', ['conversationData' => $selectedConversation]);
    $html = $response->html();
    $_instance->logRenderedChild($selectedConversation['id'], $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
            <?php else: ?>
                <div class="flex items-center justify-center h-full w-full bg-white text-gray-500">
                    <div class="text-center">
                        <h3 class="text-lg font-medium">Select a conversation</h3>
                        <p class="mt-1 text-sm">Start chatting by selecting a conversation from the list.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/messenger.blade.php ENDPATH**/ ?>