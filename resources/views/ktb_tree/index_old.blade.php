<x-layouts.app title="Pohon KTB">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Pohon KTB</h1>
            <div class="flex gap-2">
                <button id="zoomIn" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Zoom In
                </button>
                <button id="zoomOut" class="rounded-lg bg-zinc-600 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700">
                    Zoom Out
                </button>
                <button id="resetZoom" class="rounded-lg bg-zinc-600 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700">
                    Reset
                </button>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div id="tree-container" style="height: 700px; overflow: auto; background: #f8fafc;" class="dark:bg-zinc-800"></div>
        </div>

        <!-- Legend -->
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <h3 class="mb-3 font-semibold text-zinc-900 dark:text-white">Legend:</h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-4 rounded bg-green-500"></div>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Active</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-4 w-4 rounded bg-blue-500"></div>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Alumni</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-4 w-4 rounded bg-red-500"></div>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Inactive</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tree-node {
            position: absolute;
            padding: 12px 20px;
            border-radius: 8px;
            border: 2px solid #3b82f6;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            min-width: 180px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .tree-node:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .tree-node.status-active {
            border-color: #22c55e;
            background: #f0fdf4;
        }
        .tree-node.status-alumni {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .tree-node.status-inactive {
            border-color: #ef4444;
            background: #fef2f2;
        }
        .tree-node .name {
            font-weight: 600;
            font-size: 14px;
            color: #18181b;
            margin-bottom: 4px;
        }
        .tree-node .info {
            font-size: 11px;
            color: #71717a;
        }
        .tree-line {
            position: absolute;
            background: #cbd5e1;
            z-index: -1;
        }
        .tree-line-vertical {
            width: 2px;
        }
        .tree-line-horizontal {
            height: 2px;
        }
        #tree-container.dark .tree-node {
            background: #27272a;
            color: #e4e4e7;
        }
        #tree-container.dark .tree-node.status-active {
            background: #14532d;
        }
        #tree-container.dark .tree-node.status-alumni {
            background: #1e3a8a;
        }
        #tree-container.dark .tree-node.status-inactive {
            background: #7f1d1d;
        }
        #tree-container.dark .tree-node .name {
            color: #fafafa;
        }
    </style>

    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
        let currentZoom = 1;
        let treeData = null;

        // Fetch tree data
        fetch('{{ route('ktb-tree.data') }}')
            .then(response => response.json())
            .then(data => {
                treeData = data;
                renderTree(data);
            })
            .catch(error => {
                console.error('Error loading tree data:', error);
                document.getElementById('tree-container').innerHTML = 
                    '<div class="flex items-center justify-center h-full text-red-600">Error loading tree data</div>';
            });

        function renderTree(data) {
            const container = document.getElementById('tree-container');
            container.innerHTML = '';

            const width = container.offsetWidth;
            const levelHeight = 150;
            const nodeWidth = 200;
            const nodePadding = 50;

            // Create SVG
            const svg = d3.create('svg')
                .attr('width', width)
                .attr('height', 1000)
                .style('overflow', 'visible');

            const g = svg.append('g')
                .attr('class', 'tree-group')
                .attr('transform', `translate(${width/2}, 50)`);

            // Create tree layout
            const treeLayout = d3.tree()
                .size([width - 200, 800])
                .separation((a, b) => (a.parent == b.parent ? 1 : 1.2));

            // Create hierarchy
            const root = d3.hierarchy({
                name: 'Root',
                id: 0,
                children: data
            });

            treeLayout(root);

            // Draw links
            g.selectAll('.link')
                .data(root.links().filter(d => d.source.depth > 0))
                .join('path')
                .attr('class', 'link')
                .attr('fill', 'none')
                .attr('stroke', '#94a3b8')
                .attr('stroke-width', 2)
                .attr('d', d3.linkVertical()
                    .x(d => d.x)
                    .y(d => d.y));

            // Draw nodes
            const nodes = g.selectAll('.node')
                .data(root.descendants().filter(d => d.depth > 0))
                .join('g')
                .attr('class', 'node')
                .attr('transform', d => `translate(${d.x},${d.y})`)
                .style('cursor', 'pointer')
                .on('click', (event, d) => {
                    window.location.href = `/ktb-members/${d.data.id}`;
                });

            // Add colored circles
            nodes.append('circle')
                .attr('r', 25)
                .attr('fill', d => {
                    if (d.data.status === 'active') return '#22c55e';
                    if (d.data.status === 'alumni') return '#3b82f6';
                    return '#ef4444';
                })
                .attr('stroke', '#fff')
                .attr('stroke-width', 3);

            // Add text - name
            nodes.append('text')
                .attr('dy', '0.35em')
                .attr('text-anchor', 'middle')
                .style('fill', '#fff')
                .style('font-weight', '600')
                .style('font-size', '11px')
                .style('pointer-events', 'none')
                .text(d => {
                    const name = d.data.name;
                    return name.length > 10 ? name.substring(0, 10) + '...' : name;
                });

            // Add generation label below node
            nodes.append('text')
                .attr('dy', '45px')
                .attr('text-anchor', 'middle')
                .style('fill', '#71717a')
                .style('font-size', '12px')
                .style('font-weight', '600')
                .text(d => d.data.name);

            nodes.append('text')
                .attr('dy', '60px')
                .attr('text-anchor', 'middle')
                .style('fill', '#a1a1aa')
                .style('font-size', '10px')
                .text(d => `Gen ${d.data.generation} • ${d.data.children?.length || 0} mentees`);

            // Append to container
            container.appendChild(svg.node());

            // Apply initial zoom
            applyZoom(currentZoom);
        }

        function applyZoom(scale) {
            const svg = document.querySelector('#tree-container svg');
            const g = svg.querySelector('.tree-group');
            if (g) {
                const transform = g.getAttribute('transform');
                const match = transform.match(/translate\(([^,]+),\s*([^)]+)\)/);
                if (match) {
                    const x = parseFloat(match[1]);
                    const y = parseFloat(match[2]);
                    g.setAttribute('transform', `translate(${x}, ${y}) scale(${scale})`);
                }
            }
        }

        // Zoom controls
        document.getElementById('zoomIn').onclick = function() {
            currentZoom = Math.min(currentZoom + 0.2, 3);
            applyZoom(currentZoom);
        };

        document.getElementById('zoomOut').onclick = function() {
            currentZoom = Math.max(currentZoom - 0.2, 0.5);
            applyZoom(currentZoom);
        };

        document.getElementById('resetZoom').onclick = function() {
            currentZoom = 1;
            applyZoom(currentZoom);
        };
    </script>
</x-layouts.app>

