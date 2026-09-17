import React from 'react';
import { Dropdown, Button, Space } from 'antd';
import { BgColorsOutlined } from '@ant-design/icons';
import { useDensity } from '@/hooks/useDensity';
import { SLV_DENSITY_LABELS, type SLV_Density } from '@/config/theme';

export const DensitySwitcher: React.FC = () => {
    const [density, setDensity] = useDensity();

    const items = Object.entries(SLV_DENSITY_LABELS).map(([ key, label ]) => ({
        key,
        label,
        onClick: () => setDensity(key as SLV_Density),
    }));

    return (
        <Dropdown menu={{ items, selectedKeys: [density] }} placement="bottomRight">
            <Button type="text" icon={<BgColorsOutlined />}>
                {SLV_DENSITY_LABELS[density]}
            </Button>
        </Dropdown>
    );
};