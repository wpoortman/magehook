<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Event\Config;

use Magento\Framework\Event\Config\SchemaLocator as EventSchemaLocator;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;

/**
 * Class SchemaLocator
 *
 * @package MageHook\Hook\Event\Config
 */
class SchemaLocator extends EventSchemaLocator
{
    /**
     * Path to corresponding XSD file with validation rules for merged config.
     *
     * @var null|string
     */
    protected $_schema;

    /**
     * Path to corresponding XSD file with validation rules for separate config files.
     *
     * @var null|string
     */
    protected $_perFileSchema;

    /**
     * SchemaLocator constructor.
     *
     * @param Reader $moduleReader
     */
    public function __construct(
        Reader $moduleReader
    ) {
        $dir = $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'MageHook_Hook');
        $this->_schema = $dir . '/webhooks.xsd';
    }

    /**
     * Get path to merged config schema.
     *
     * @return string|null
     */
    public function getSchema(): ?string
    {
        return $this->_schema;
    }

    /**
     * Get path to per file validation schema.
     *
     * @return string|null
     */
    public function getPerFileSchema(): ?string
    {
        return $this->_perFileSchema;
    }
}
