<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container2KXDDT6\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container2KXDDT6/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container2KXDDT6.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container2KXDDT6\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \Container2KXDDT6\App_KernelDevDebugContainer([
    'container.build_hash' => '2KXDDT6',
    'container.build_id' => '8780e75e',
    'container.build_time' => 1753443273,
    'container.runtime_mode' => \in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) ? 'web=0' : 'web=1',
], __DIR__.\DIRECTORY_SEPARATOR.'Container2KXDDT6');
