import React from 'react'

import './styles.css'
import Tree from '@naisutech/react-tree';

class CurriculumTree extends React.Component {

  constructor(props) {
    super(props)


    this.state = {
      urlTreeService: document.getElementById('treeservice').textContent,
      nodeIdToLoad: document.getElementById('treeservice-nodeid').textContent,
      nodeDepthToLoad: 1,
      loadedTreeNodeIds: [],
      nodesOfTree: [],
      alreadyinTree: [],
      showLoadingSpinner: 1,
    }

    this.loadTreeSection(
      this.state.nodeIdToLoad,
      this.state.nodeDepthToLoad,
      this.state.loadedTreeNodeIds,
      this.state.nodesOfTree,
      this.state.alreadyinTree)
  }

  loadTreeSection(nodeIdToLoad,
                  nodeDepthToLoad,
                  loadedTreeNodeIds,
                  nodesOfTree,
                  alreadyinTree) {

    const params = new URLSearchParams()
    params.append('node_id', nodeIdToLoad)
    params.append('node_depth', nodeDepthToLoad)

    fetch(this.state.urlTreeService + "&" + params).then(res => res.json())
    .then(json => {
      let tree_node
      for(let i = 0; i < json.length; i++) {
          tree_node =json[i];
          console.log(tree_node.id)
        if(this.checkIfLoaded(alreadyinTree, tree_node.id) === false) {
          console.log('LOAD')
          this.state.nodesOfTree.push(tree_node);
          this.state.alreadyinTree.push(tree_node.id);
        }
      }

      this.setState({
        loadedTreeNodeIds: [...this.state.loadedTreeNodeIds, nodeIdToLoad],
        nodesOfTree: this.state.nodesOfTree,
        alreadyinTree: this.state.alreadyinTree,
       showLoadingSpinner: ''
      })
    })
  }


  onSelect = (selectedNode) => {
    if(selectedNode.type == 'cat') {
      this.onSelectilCategory(selectedNode)
    } else {
      this.onSelectilItem(selectedNode)
    }
  }

  onSelectilCategory(selectedCategoryNode) {
    const { urlTreeService, nodeIdToLoad, nodeDepthToLoad, loadedTreeNodeIds, nodesOfTree, alreadyinTree,showLoadingSpinner } = this.state;
    if (this.checkIfLoaded(loadedTreeNodeIds, selectedCategoryNode.id) === false) {
      this.setState({showLoadingSpinner: 1})
      this.loadTreeSection(
        selectedCategoryNode.id, selectedCategoryNode.depth, loadedTreeNodeIds, nodesOfTree, alreadyinTree)
    }
  }

  onSelectilItem(selectedItemNode) {
    const { urlTreeService, nodeIdToLoad, nodeDepthToLoad, loadedTreeNodeIds, nodesOfTree, alreadyinTree,showLoadingSpinner } = this.state;
    console.log(selectedItemNode.link)
    this.setState({showLoadingSpinner: 1})
    window.location.href = selectedItemNode.link
  }

  render() {
    const { urlTreeService, nodeIdToLoad, nodeDepthToLoad, loadedTreeNodeIds, nodesOfTree, showLoadingSpinner } = this.state;
        return (<div className="showtree" style={{ display: 'flex', flexWrap: 'nowrap', flexGrow: 1 }}>
          <div style={{ display: 'flex', flexWrap: 'nowrap', flexGrow: 1}}>
            <Tree nodes={nodesOfTree}  theme={'light'} onSelect={this.onSelect} isLoading={showLoadingSpinner} />
          </div>
        </div>)
  }

  checkIfLoaded(loadedTreeNodeIds, nodeIdToLoad) {
    return loadedTreeNodeIds.indexOf(nodeIdToLoad) >= 0
  }
}

export default CurriculumTree;
CurriculumTree.propTypes = {}